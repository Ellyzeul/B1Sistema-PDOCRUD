<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Actions\Tracking\DeliveryMethods\Correios;
use App\Actions\Tracking\DeliveryMethods\Jadlog;
use App\Actions\Tracking\DeliveryMethods\DHL;

class UpdateOrInsertPurchaseTrackingAction
{
    public function handle(string $trackingCode, string | null $deliveryMethod)
    {
        return $this->updateOrInsertPurchaseTracking($trackingCode, $deliveryMethod);
    }

	private function updateOrInsertPurchaseTracking(string $trackingCode, string | null $deliveryMethod)
	{
		if(!isset($this->supplierSupportedServices[$deliveryMethod])) return ["Serviço não suportado", 400];

		$response = null;
		if($deliveryMethod == "Correios" || $deliveryMethod == "Correios Reverso") $response = (new Correios())->fetch($trackingCode);
		if($deliveryMethod == "Jadlog") $response = (new Jadlog())->fetch($trackingCode);
		
		if($deliveryMethod == "DHL"){
			$DHL = new DHL();
			$response = $DHL->fetchDHLMyAPI($trackingCode);

			$response = 
				$response === []
					? $DHL->fetchDHLShipmentTrackingUnified($trackingCode) 
					: $response;
		} 

		if (!isset($response['client_deadline'])) {
			$response['deadline'] = $response['client_deadline'] ?? null;
			unset($response['client_deadline']);
		}

		if(count($response) > 0) $response['api_calling_date'] = date("Y-m-d");
		
		DB::table('purchase_trackings')->updateOrInsert(
			['tracking_code' => $trackingCode],
			isset($response)
			? $response
			: []
		);
		
		return isset($response)
			? [$response, 200]
			: ["Erro na atualização", 500];
	}    

    private array $supplierSupportedServices = [
		"Correios" => true,
		"Correios Reverso" => true,
		"Jadlog" => true,
		"DHL" => true,
	];
}