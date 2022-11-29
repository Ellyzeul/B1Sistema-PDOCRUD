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
		"Correios" => true
	];

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
				'trackings.observation',
			)
			->get();
		
		return $results;
	}

	public function updateOrInsertTracking(string $trackingCode, string $deliveryMethod)
	{
		if(!isset($this->supportedServices[$deliveryMethod])) return ["Serviço não suportado", 400];

		$response = null;
		if($deliveryMethod == "Correios") $response = $this->fetchCorreios($trackingCode);

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

	private function fetchCorreios(string $trackingCode)
	{
		$response = Http::withBasicAuth(env('CORREIOS_USERNAME'), env('CORREIOS_PASSWORD'))
			->post('https://apps3.correios.com.br/areletronico/v1/ars/ultimoevento', [
				'objetos' => [$trackingCode]
			])[0]['eventos'][0];
		
		return [
			"status" => $response['descricaoEvento'],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $response['dataEvento']))),
			"details" => "{$response['nomeUnidade']} - {$response['municipio']} - {$response['uf']}",
		];
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

			$this->updateOrInsertTracking(
				$result->tracking_code, 
				$result->delivery_method
			);
		}
	}
}
