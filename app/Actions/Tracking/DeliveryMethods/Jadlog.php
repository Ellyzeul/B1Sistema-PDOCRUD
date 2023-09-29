<?php namespace App\Actions\Tracking\DeliveryMethods;

use Illuminate\Support\Facades\Http;

class Jadlog
{
	public function fetch(string $shipmentId)
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

	public function consultPrice(string $originZipCode, string $destinyZipCode, float|null $weight) 
	{
		$response = Http::withToken(env('JADLOG_API_KEY'))
		->post('https://www.jadlog.com.br/embarcador/api/frete/valor', [
			'frete' => [[
				"cepori" => $originZipCode,
				"cepdes" => $destinyZipCode,
				"peso" =>  $weight ?? 1,
				"modalidade" => 3,
				"tpentrega" => "D",
				"tpseguro" => "N",
				"vldeclarado" => 100,
			]]
		]);

		return [
			'price' => $response['frete'][0]['vltotal'] ?? null,
			'max_date' => $response['frete'][0]['prazo'] ?? null,
			'error_msg' => $response['error']['descricao'] ?? null
		];
	}	
}