<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Date;

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
		if($deliveryMethod == "DHL") $response = $this->fetchDHL($trackingCode);
		if($deliveryMethod == "FedEx") $response = $this->fetchFedex($trackingCode);

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

		$json = json_decode($this->readApiCredentialDB('correios')->key);
		$CORREIOS_API_KEY = $json->token;
		$expires_in = explode("T", $json->expiraEm)[0];

		if((!$CORREIOS_API_KEY) || Date::parse($expires_in)->diffAsCarbonInterval($today)->format("%d")!=1){
			$this->generateCorreiosToken();
			$json = json_decode(json_decode($this->readApiCredentialDB('correios')->key));
			$CORREIOS_API_KEY = $json->token;
		} 

		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->withToken($CORREIOS_API_KEY)
			->get("https://api.correios.com.br/srorastro/v1/objetos/$trackingCode");

		if(!isset($response['objetos'][0]['eventos'][0])) return [];
		$response = $response['objetos'][0]['eventos'][0];

		$street = $response['unidade']['endereco']['logradouro'] ?? "";
		$complement = $response['unidade']['endereco']['complemento'] ?? "";
		$number = $response['unidade']['endereco']['numero'] ?? "";
		$district = $response['unidade']['endereco']['bairro'] ?? "";
		$cep = $response['unidade']['endereco']['cep'] ?? "";
		
		$info = $response['detalhe'] ?? null;

		return [
			"status" => $response['descricao'],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $response['dtHrCriado']))),
			"details" => "{$response['unidade']['tipo']}" 
						." - {$response['unidade']['endereco']['cidade']}" 
						." - {$response['unidade']['endereco']['uf']} "
						."$street $complement $number $district $cep $info",

			"client_deadline" => $response['dtLimiteRetirada'] ?? null,
		];
	}

	private function fetchJadlog(string $shipmentId)
	{
		$response = Http::withToken(env('JADLOG_API_KEY'))
			->post('www.jadlog.com.br/embarcador/api/tracking/consultar', [
				'consulta' => [['shipmentId' => $shipmentId]]
			]);

		if(!isset($response['consulta'][0]['tracking']['eventos'][0])) return [];

		$deliveryExpectedDate = $response['consulta'][0]['previsaoEntrega'] ?? null;
		$eventsList = $response['consulta'][0]['tracking']['eventos'];
		$lastEvent = array_pop($eventsList);

		$toReturn = [
			"status" => $lastEvent['status'],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $lastEvent['data']))),
			"details" => $lastEvent['unidade'],
		];
		if(isset($deliveryExpectedDate)) $toReturn["delivery_expected_date"] = $deliveryExpectedDate;

		return $toReturn;
	}

	private function fetchDHL(string $trackingCode)
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

		$json = json_decode($this->readApiCredentialDB('fedex')->key);

		if($json == ""){
			$this->generateFedexToken();
			$json = json_decode($this->readApiCredentialDB('fedex')->key);
		} 
		
		$FEDEX_API_TOKEN = $json->access_token;
	
		var_dump($FEDEX_API_TOKEN);
		
		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->withToken($FEDEX_API_TOKEN)
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
		

		if($response->getStatusCode() == 401) {
			echo "entrou";
			$this->writeApiCredentialDB('fedex', "");
			return $this->fetchFedex($trackingCode);
		}

		$scanEvents = $response['output']['completeTrackResults'][0]['trackResults'][0]['scanEvents'][0];
		
		$lastUpdateDate = $scanEvents['date'];

		$exceptionDescription = $scanEvents['exceptionDescription'] ?? "";
		$scanLocation = $scanEvents['scanLocation'];
		$city = $scanLocation['city'] ?? "";
		$stateOrProvinceCode = $scanLocation['stateOrProvinceCode'] ?? "";
		$postalCode = $scanLocation['postalCode'] ?? "";
		$countryName = $scanLocation['countryName'] ?? "";

		return [
			'status' => "{$scanEvents['eventDescription']}",
			"last_update_date" => date('Y-m-d', strtotime($lastUpdateDate)),
			"details" => "$city $stateOrProvinceCode $postalCode $countryName $exceptionDescription"
						
		];
	}

	private function generateFedexToken()
	{
		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->asForm()
			->post('https://apis.fedex.com/oauth/token', [
				"grant_type" => "client_credentials",
				"client_id" => env('FEDEX_CLIENT_ID'),
				"client_secret" => env('FEDEX_CLIENT_SECRET')
			]);

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
		$credential = DB::table('api_credentials')
			->select('key')
			->where('id', $id)
			->first();

		return $credential;
	}

	private function writeApiCredentialDB(string $id, string $key)
	{
		DB::table('api_credentials')
			->upsert([
		  		'id' => $id,
		  		'key' => $key
			], ['key']);
	}
	
	private function existsApiCredentialDB(string $id)
	{
		$credential = DB::table('api_credentials')
			->where('id', $id)
			->exists(); 

		return $credential;
	}
}
