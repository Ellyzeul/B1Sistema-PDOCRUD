<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Date;

use function PHPUnit\Framework\matches;

class Tracking extends Model
{
	use HasFactory;

	private array $supportedServices = [
		"Correios" => true,
		"Jadlog" => true,
		"DHL" => true,
		"FedEx" => true,
	];

	public function read()
	{
		$results = DB::table('trackings')
			->join('order_control', 'trackings.tracking_code', '=', 'order_control.tracking_code')
			->select(
				'trackings.tracking_code',
				(DB::raw('(SELECT name FROM delivery_methods WHERE id = order_control.id_delivery_method) as delivery_method')),
				'order_control.online_order_number',
				'trackings.status',
				'trackings.last_update_date',
				'trackings.details',
				'order_control.expected_date',
				'trackings.delivery_expected_date',
				'trackings.client_deadline',
				'trackings.api_calling_date',
				'trackings.observation',
			)
			->where('order_control.id_phase', '=', '5.1')
			->orWhere('order_control.id_phase', '=', '5.2')
			->get();
		
		return $results;
	}

	public function readForExcel(array $orderNumbers)
	{
		$results = DB::table('order_control')
			->whereIn('online_order_number', $orderNumbers)
			->select(
				'id',
				'online_order_number',
				'tracking_code',
				'delivered_date',
			)
			->get();
		
		return [
			"columns" => Order::getColumnsNames(),
			"data" => $results
		];
	}

	public function updateOrInsertTracking(string $trackingCode, string | null $deliveryMethod)
	{
		if(!isset($this->supportedServices[$deliveryMethod])) return ["Serviço não suportado", 400];

		$response = null;
		if($deliveryMethod == "Correios") $response = $this->fetchCorreios($trackingCode);
		if($deliveryMethod == "Jadlog") $response = $this->fetchJadlog($trackingCode);
		if($deliveryMethod == "FedEx") $response = $this->fetchFedex($trackingCode);
		
		if($deliveryMethod == "DHL"){
			$response = $this->fetchDHLMyAPI($trackingCode);

			$response = 
				$response === []
					? $this->fetchDHLShipmentTrackingUnified($trackingCode) 
					: $response;
		} 

		if(count($response) > 0) $response['api_calling_date'] = date("Y-m-d");

		DB::table('trackings')->updateOrInsert(
			['tracking_code' => $trackingCode],
			isset($response)
				? $response
				: []
		);

		return isset($response)
			? [$response, 200]
			: ["Erro na atualização", 500];
	}

	public function updateField(string $trackingCode, string $field, string $value)
	{
		DB::table('trackings')
			->where('tracking_code', '=', $trackingCode)
			->update([
				$field => $value
			]);
		
		return [
			"message" => "Campo atualizado"
		];
	}

	private function fetchCorreios(string $trackingCode)
	{	
		$today = Date::today();

		if(!$this->existsApiCredentialDB('correios')) $this->generateCorreiosToken();

		$apikey = $this->readApiCredentialDB('correios');
		$expires_in = explode("T", $apikey->expiraEm)[0];

		if((!$apikey->token) || Date::parse($expires_in)->diffAsCarbonInterval($today)->format("%d")!=1){
			$this->generateCorreiosToken();
			$apikey = $this->readApiCredentialDB('correios');
		} 

		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->withToken($apikey->token)
			->get("https://api.correios.com.br/srorastro/v1/objetos/$trackingCode");

		if(!isset($response['objetos'][0]['eventos'][0])) return [];
		$response = $response['objetos'][0]['eventos'][0];

		$street = $response['unidade']['endereco']['logradouro'] ?? "";
		$complement = $response['unidade']['endereco']['complemento'] ?? "";
		$number = $response['unidade']['endereco']['numero'] ?? "";
		$district = $response['unidade']['endereco']['bairro'] ?? "";
		$cep = $response['unidade']['endereco']['cep'] ?? "";
		$city = $response['unidade']['endereco']['cidade'] ?? "";
		$info = $response['detalhe'] ?? null;

		return [
			"status" => $response['descricao'],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $response['dtHrCriado']))),
			"details" => "{$response['unidade']['tipo']}" 
						." - {$city}" 
						." - {$response['unidade']['endereco']['uf']} "
						."$street $complement $number $district $cep $info",

			"client_deadline" => $response['dtLimiteRetirada'] ?? null,
		];
	}

	private function fetchJadlog(string $shipmentId)
	{
		$identifier = substr($shipmentId,0,2);
		$type = $identifier == "11" ? "shipmentId" : "cte";

		$response = Http::withToken(env('JADLOG_API_KEY'))
			->post('www.jadlog.com.br/embarcador/api/tracking/consultar', [
				'consulta' => [[$type => $shipmentId]]
			]);

		if(!isset($response['consulta'][0]['tracking']['eventos'][0])) return [];

		$deliveryExpectedDate = $response['consulta'][0]['previsaoEntrega'] ?? null;
		$eventsList = $response['consulta'][0]['tracking']['eventos'];
		$lastEvent = array_pop($eventsList);
		$error = $response['consulta'][0]['erro']['descricao'] ?? "";
		$errorDetails = $response['consulta'][0]['erro']['detalhe'] ?? "";

		$toReturn = [
			"status" => $lastEvent['status'],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $lastEvent['data']))),
			"details" => "{$lastEvent['unidade']} $error $errorDetails",
		];
		if(isset($deliveryExpectedDate)) $toReturn["delivery_expected_date"] = $deliveryExpectedDate;

		return $toReturn;
	}

	private function fetchDHLMyAPI(string $trackingCode)
	{
		$response = Http::withHeaders(["Accept-Language" => "pt-BR"])
			->withBasicAuth(env('DHL_API_CLIENT_ID'), env('DHL_API_CLIENT_SECRET'))
			->get("https://express.api.dhl.com/mydhlapi/shipments/$trackingCode/tracking");

		if(!isset($response) || !isset($response['shipments'])) return [];
		
		$events = $response['shipments'][0]['events'];
		$last = sizeof($events)-1;

		$lastUpdateDate = $response['shipments'][0]['events'][$last]['date'];
		$lastUpdateDescription = "{$response['shipments'][0]['events'][$last]['description']}\n";
		$deliveryExpectedDate = $response['shipments'][0]['estimatedDeliveryDate'] ?? null;

		if($last>=1){
			$penultimate = $last-1;
			$penultimateUpdateDate = $response['shipments'][0]['events'][$penultimate]['date'] ?? "";
			$penultimateUpdateDescription = "{$response['shipments'][0]['events'][$penultimate]['description']}\n" ?? "";

			if($penultimate>=1){
				$antepenultimate = $penultimate-1;
				$antepenultimateUpdateDate = $response['shipments'][0]['events'][$antepenultimate]['date'] ?? "";
				$antepenultimateUpdateDescription = "{$response['shipments'][0]['events'][$antepenultimate]['description']}\n" ?? "";
			}
		}

		$toReturn = [
			"status" => $lastUpdateDescription,
			"last_update_date" => date('Y-m-d', strtotime($lastUpdateDate)),
			"details" => date('d/m/Y', strtotime($lastUpdateDate)) .' '. $lastUpdateDescription
						.date('d/m/Y', strtotime($penultimateUpdateDate)) .' '. $penultimateUpdateDescription
						.date('d/m/Y', strtotime($antepenultimateUpdateDate)) .' '. $antepenultimateUpdateDescription
		];

		$toReturn['delivery_expected_date'] = 
			isset($deliveryExpectedDate) 
				? date('Y-m-d', strtotime($deliveryExpectedDate)) 
				: null;
		
		return $toReturn;
	}

	private function fetchDHLShipmentTrackingUnified(string $trackingCode)
	{
		$response = Http::withHeaders(["DHL-API-Key" => env('DHL_API_KEY')])
			->get("https://api-eu.dhl.com/track/shipments?trackingNumber=$trackingCode&language=pt");
		
		if(!isset($response['shipments'][0]['events'][0])) return [];

		$lastEvent = $response['shipments'][0]['events'][0];
		$status = $lastEvent['description'];
		$lastUpdateDate = $lastEvent['timestamp'];
		$deliveryExpectedDate = $response['shipments'][0]['estimatedTimeOfDelivery'] ?? null;
		$details = ($lastEvent['remark'] ?? "") . " " . ($lastEvent['nextSteps'] ?? "");

		$toReturn = [
			"status" => $status,
			"last_update_date" => date('Y-m-d', strtotime($lastUpdateDate)),
			"details" => $details
		];
		isset($deliveryExpectedDate) ? $toReturn['delivery_expected_date'] = $deliveryExpectedDate : null;

		return $toReturn;
	}

	private function fetchFedex(string $trackingCode)
	{
		if(!$this->existsApiCredentialDB('fedex')) $this->generateFedexToken();

		$apikey = $this->readApiCredentialDB('fedex');

		if(!isset($apikey)){
			$this->generateFedexToken();
			$apikey = $this->readApiCredentialDB('fedex');
		}
		
		try {
			$response = Http::withHeaders(["X-locale" => "pt_BR"])
				->withToken($apikey->access_token)
				->post('https://apis.fedex.com/track/v1/trackingnumbers', [
					"trackingInfo" => [
						[
							"trackingNumberInfo" => [
								"trackingNumber" => $trackingCode
							]
						]
					],
					"includeDetailedScans" => true
				]);
		}
		catch(Exception $e) {
			return $this->fetchFedex($trackingCode);
		}

		if($response->getStatusCode() == 401) {
			$this->writeApiCredentialDB('fedex', null);
			return $this->fetchFedex($trackingCode);
		}

		$scanEvents = $response['output']['completeTrackResults'][0]['trackResults'][0]['scanEvents'];

		$count = 0;
		$details = "";
		while(sizeof($scanEvents)>=0 && $count<=2){
			$lastUpdateDate = date('Y-m-d', strtotime($scanEvents[$count]['date']));
			$exceptionDescription = $scanEvents[$count]['exceptionDescription'] ?? "";
			$scanLocation = $scanEvents[$count]['scanLocation'];
			$city = $scanLocation['city'] ?? "";
			$stateOrProvinceCode = $scanLocation['stateOrProvinceCode'] ?? "";
			$postalCode = $scanLocation['postalCode'] ?? "";
			$countryName = $scanLocation['countryName'] ?? "";

			$details = $details . "$lastUpdateDate $city $stateOrProvinceCode $postalCode $countryName $exceptionDescription\n";
			$count++;
		}

		if(isset($response['output']['completeTrackResults'][0]['trackResults'][0]['serviceCommitMessage'])){
			$msg = $response['output']['completeTrackResults'][0]['trackResults'][0]['serviceCommitMessage']['message'];
			preg_match("/[0-9]{1,2}\sde\s([A-Za-zç]*)\sde\s[0-9]{4}/", $msg, $matches);
			
			if($matches) $deadline = date('Y-m-d', strtotime("+5 weekdays", strtotime($this->datePTBRToDate($matches[0]))));
		}
		
		return [
			'status' => $scanEvents[0]['eventDescription'], 
			'last_update_date' => $lastUpdateDate, 
			'details' => $details, 
			"client_deadline" => $deadline ?? null,
		];
	}

	private function generateFedexToken()
	{
		try {
			$response = Http::withHeaders(['X-locale' => 'pt_BR'])
				->asForm()
				->post('https://apis.fedex.com/oauth/token', [
					'grant_type' => 'client_credentials',
					'client_id' => env('FEDEX_CLIENT_ID'),
					'client_secret' => env('FEDEX_CLIENT_SECRET')
				]);
		}
		catch(Exception $e) {
			$this->generateFedexToken();
			return;
		}

		$this->writeApiCredentialDB('fedex', $response);
	}

	private function generateCorreiosToken()
	{
		$response = Http::withBasicAuth(env('CORREIOS_USERNAME'), env('CORREIOS_PASSWORD'))
			->post('https://api.correios.com.br/token/v1/autentica/cartaopostagem', [
				'numero' => env('CORREIOS_CARTAO_POSTAGEM')
			]);

		$this->writeApiCredentialDB('correios', $response);
	}

	private function updateDB()
	{
		$results = DB::table('order_control')
			->select(
				'tracking_code', 
				(DB::raw('(SELECT name FROM delivery_methods WHERE id = order_control.id_delivery_method) as delivery_method'))
			)
			->whereRaw("
				tracking_code NOT IN (SELECT tracking_code FROM trackings)
				AND (id_phase = '5.1' OR id_phase = '5.2')
				AND LENGTH(tracking_code) > 0
			")
			->get();
		$inserts = [];
		
		foreach($results as $result) {
			$toPush = ['tracking_code' => $result->tracking_code];

			try {
				$this->updateOrInsertTracking(
					$result->tracking_code, 
					$result->delivery_method
				);
			}
			catch(Exception $e) {
				continue;
			}
		}
	}

	private function readApiCredentialDB(string $id)
	{
		$json = DB::table('api_credentials')
			->select('key')
			->where('id', $id)
			->first()
			->key;

		return json_decode($json);
	}

	private function writeApiCredentialDB(string $id, string | null $key)
	{
		DB::table('api_credentials')
			->upsert([
				'id' => $id,
				'key' => $key
			], ['key']);
	}
	
	private function existsApiCredentialDB(string $id)
	{
		return DB::table('api_credentials')
			->where('id', $id)
			->exists();
	}

	private function datePTBRToDate($dateString)
	{
	  return preg_replace(
		['/\sde\s/', '/Janeiro/', '/Fevereiro/', '/Março/', '/Abril/', '/Maio/', '/Junho/', '/Julho/', '/Agosto/', '/Setembro/', '/Outubro/', '/Novembro/', '/Dezembro/'],
		['-', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', ], 
		$dateString
	  );
	}
}
