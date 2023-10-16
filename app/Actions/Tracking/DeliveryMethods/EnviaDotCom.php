<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Services\ThirdParty\EnviaDotCom as API;

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
}