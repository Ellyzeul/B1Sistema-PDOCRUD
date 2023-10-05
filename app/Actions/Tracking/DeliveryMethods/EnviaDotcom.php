<?php namespace App\Actions\Tracking\DeliveryMethods;

use Illuminate\Support\Facades\Http;

class EnviaDotcom
{
	public function fetch(string $tracking_code)
	{
		$response = Http::enviaCom(env('ENVIA_DOT_COM_API_TOKEN'))
			->get("/guide/$tracking_code")
			->json();

		$tracking = array_pop($response)[0];

		return [
			"status" => $this->statusMessage[$tracking["status"]] ?? $tracking["status"],
			"last_update_date" => isset($tracking["delivered_at"])
				? date('Y-m-d', strtotime(str_replace('/', '-', $tracking["delivered_at"])))
				: null,
			"details" => (isset($tracking["created_at"]) ? "Criado: " . date('Y-m-d', strtotime(str_replace('/', '-', $tracking["created_at"]))) : null) .
					(isset($tracking["shipped_at"]) ? "\nEnviado: " . date('Y-m-d', strtotime(str_replace('/', '-', $tracking["shipped_at"]))) : null) .
					(isset($tracking["delivered_at"]) ? "\nEntregue: " . date('Y-m-d', strtotime(str_replace('/', '-', $tracking["delivered_at"]))) : null),												
			"client_deadline" => null,
		];        
	}

	private array $statusMessage = [
	"Created" => "Criado",
	"Delivered" => "Entregue",
	"Canceled" => "Cancelado",
];
}