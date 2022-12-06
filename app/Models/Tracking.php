<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Tracking extends Model
{
	use HasFactory;

	private array $supportedServices = [
		"Correios" => true,
		"Jadlog" => true,
		"DHL" => true,
		// "FedEx" => true,
	];
	private static string | null $fedexToken = null;

	public function read()
	{
		$this->updateDB();

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
				'id_phase',
				'id_delivery_method',
				'tracking_code',
				'delivered_date',
				'ask_rating'
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
		// if($deliveryMethod == "FedEx") $response = $this->fetchFedex($trackingCode);

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
		$response = Http::withBasicAuth(env('CORREIOS_USERNAME'), env('CORREIOS_PASSWORD'))
			->post('https://apps3.correios.com.br/areletronico/v1/ars/ultimoevento', [
				'objetos' => [$trackingCode]
			]);
		
		if(!isset($response[0]['eventos'][0])) return [];
		$response = $response[0]['eventos'][0];

		return [
			"status" => $response['descricaoEvento'],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $response['dataEvento']))),
			"details" => "{$response['nomeUnidade']} - {$response['municipio']} - {$response['uf']}",
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

	// private function fetchFedex(string $trackingCode)
	// {
	// 	if(!isset(Tracking::$fedexToken)) $this->generateFedexToken();
	// 	$response = Http::withHeaders(["X-locale" => "pt_BR"])
	// 		->withToken(Tracking::$fedexToken)
	// 		->post('https://apis.fedex.com/track/v1/associatedshipments', [
	// 			"masterTrackingNumberInfo" => [
	// 				"trackingNumberInfo" => [
	// 					"trackingNumber" => $trackingCode
	// 				]
	// 			],
	// 			"associatedType" => "STANDARD_MPS"
	// 		]);
		
	// 	if($response->getStatusCode() == 401) {
	// 		Tracking::$fedexToken = null;
	// 		return $this->fetchFedex($trackingCode);
	// 	}

	// 	return [];
	// }

	// private function generateFedexToken()
	// {
	// 	$response = Http::withHeaders(["X-locale" => "pt_BR"])
	// 		->asForm()
	// 		->post('https://apis.fedex.com/oauth/token', [
	// 			"grant_type" => "client_credentials",
	// 			"client_id" => env('FEDEX_CLIENT_ID'),
	// 			"client_secret" => env('FEDEX_CLIENT_SECRET')
	// 		]);

	// 	var_dump($response);
	// 	Tracking::$fedexToken = $response['access_token'];
	// }

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
}
