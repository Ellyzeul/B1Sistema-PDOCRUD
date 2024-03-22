<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Models\Order;
use App\Services\ThirdParty\EnviaDotCom as API;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnviaDotCom
{
	private array $statusMessage = [
		"Created" => "Criado",
		"Shipped" => "Enviado",
		"Delivered" => "Entregue",
		"Canceled" => "Cancelado",
		"Delayed" => "Atrasado",
	];

	public function fetch(string $trackingNumber)
	{
		$response = (new API())->getShipment($trackingNumber);

		$tracking = array_pop($response)[0];

		return [
			"status" => $this->statusMessage[$tracking["status"]] ?? $tracking["status"],
			"last_update_date" => isset($tracking["delivered_at"])
				? date('Y-m-d', strtotime(str_replace('/', '-', $tracking["delivered_at"])))
				: null,
			"details" => 
				$this->formatDateMessage('Criado', $tracking["created_at"]) .
				$this->formatDateMessage('Enviado', $tracking["shipped_at"]) .
				$this->formatDateMessage('Entregue', $tracking["delivered_at"]), 
			"client_deadline" => null,
		];        
	}

	private function formatDateMessage(string $msg, string | null $date)
	{
		if(!isset($date)) return '';

		return "$msg: " . date('Y-m-d', strtotime(str_replace('/', '-', $date))) . "\n";
	}

	public function postQuoteShipment(string $orderId, string $originPostalCode, string $clientPostalCode, float $weight)
	{
		$clientUF = DB::table('order_addresses')->where('online_order_number', Order::where('id', $orderId)->first()->online_order_number)
			->first()
			->state ?? "";
		$api = new API();

		$responses = [];

		try {
			foreach(['correios', 'jadlog', 'loggi'] as $courier) {
				$response = $api->postQuoteShipment(
					'SP', 
					$originPostalCode, 
					$clientUF, 
					$clientPostalCode, 
					$weight, 
					$courier
				);
				Log::debug($response);
				$responses = array_merge(
					$responses, 
					$this->mapResponse($response['data'])
				);
			}
		}
		catch(Exception) {
			return [];
		}

		return $responses;
	}

	private function mapResponse(array $response)
	{
		return array_map(fn($quote) => [
			'name' => "Envia - {$quote['serviceDescription']}",
			'price' => $quote['totalPrice'] ?? null,
			'expected_deadline' => str_replace(
				'Next day',
				'1 dia',
				str_replace('days', 'dias', $quote['deliveryEstimate'] ?? '')
			)
		], $response);
	}
}