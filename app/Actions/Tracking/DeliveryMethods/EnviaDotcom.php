<?php namespace App\Actions\Tracking\DeliveryMethods;

use Illuminate\Support\Facades\Http;
class EnviaDotcom
{
    public function fetch(string $tracking_code)
    {
        $response = Http::withToken(env('ENVIA_DOT_COM_API_TOKEN'))
            ->get("http://queries.envia.com/guide/$tracking_code")->json()->data;

		return [
			"status" => isset($this->statusMessage[$response["status"]])
                        ? $this->statusMessage[$response["status"]]
                        : $response["status"],

			"last_update_date" => isset($response["delivered_at"])
                                    ? date('Y-m-d', strtotime(str_replace('/', '-', $response["delivered_at"])))
                                    : null,

			"details" => (isset($response["created_at"])
                            ? "Criado: " . date('Y-m-d', strtotime(str_replace('/', '-', $response["created_at"]))) 
                            : null).
                        (isset($response["shipped_at"]) 
                                ? "\nEnviado: " . date('Y-m-d', strtotime(str_replace('/', '-', $response["shipped_at"]))) 
                                : null).
                        (isset($response["delivered_at"]) 
                            ? "\nEntregue: " . date('Y-m-d', strtotime(str_replace('/', '-', $response["delivered_at"]))) 
                            : null),
                            
			"client_deadline" => null,
		];        
    }

    private array $statusMessage = [
		"Created" => "Criado",
		"Delivered" => "Entregue",
		"Canceled" => "Cancelado",
	];
}