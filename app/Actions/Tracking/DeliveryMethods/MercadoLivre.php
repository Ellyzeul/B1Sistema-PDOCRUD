<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Actions\Tracking\traits\DeliveryMethodsCommon;
use App\Services\ThirdParty\MercadoLivre as API;

class MercadoLivre
{
	use DeliveryMethodsCommon;

	private const STATUS_MESSAGE = [
		"pending" => "Pendente",
		"handling" => "Em manuseio",
		"ready_to_ship" => "Pronto para o envio",
		"shipped" => "Enviado",
		"delivered" => "Entregue",
		"not_delivered" => "Não entregue",
		"cancelled" => "Cancelado",
	];

	public function fetch(string $trackingCode)
	{
		$data = $this->getOrderIdAndcompanyIdByTrackingCode($trackingCode);
		$mercadoLivre = new API($data->idCompany);

		$response = $mercadoLivre->getShipmentByOrderId($data->online_order_number);

		$status = $response->status_history;

		return [
			"status" => MercadoLivre::STATUS_MESSAGE[$response->status],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $response->last_updated))),
			"details" => 
				(isset($status->date_handling)
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
			"client_deadline" => null,
		];
	}
}