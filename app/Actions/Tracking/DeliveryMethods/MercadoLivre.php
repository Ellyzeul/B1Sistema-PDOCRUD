<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Services\ThirdParty\MercadoLivre as ML;
use Illuminate\Support\Facades\DB;

class MercadoLivre
{
    private int $idCompany;

    public function __construct(int $idCompany)
    {
      $this->idCompany = $idCompany;
    }

    public function fetch(string $orderNumber)
    {
        $mercadoLivre = new ML($this->idCompany);

        $response = $mercadoLivre->getShipmentByOrderId($orderNumber);

        $status = $response->status_history;

        return [
            "status" => $this->statusMessage[$response->status],
            "last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $response->last_updated))),
            "details" => (isset($status->date_handling)
                            ? "Manuseio: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_handling))) 
                            : null).
                        (isset($status->date_ready_to_ship) 
                            ? "\nPronto para o envio: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_ready_to_ship))) 
                            : null).
                        (isset($status->date_shipped) 
                                ? "\nEnviado: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_shipped))) 
                                : null).
                        (isset($status->date_first_visit) 
                            ? "\nPrimeira visita: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_first_visit))) 
                            : null).                            
                        (isset($status->date_not_delivered) 
                            ? "\nNão entregue: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_not_delivered))) 
                            : null).                            
                        (isset($status->date_returned) 
                            ? "\nRetornou: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_returned))) 
                            : null).                            
                        (isset($status->date_cancelled) 
                            ? "\nCancelado: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_cancelled))) 
                            : null). 
                        (isset($status->date_delivered) 
                            ? "\nEntregue: " . date('Y-m-d', strtotime(str_replace('/', '-', $status->date_delivered))) 
                            : null),
            "client_deadline" => "",
        ];
    }

    private array $statusMessage = [
		"pending" => "Pendente",
		"handling" => "Manuseio",
		"ready_to_ship" => "Pronto para o envio",
		"shipped" => "Enviado",
		"delivered" => "Entregue",
		"not_delivered" => "Não entregue",
		"cancelled" => "Cancelado",
	];
}