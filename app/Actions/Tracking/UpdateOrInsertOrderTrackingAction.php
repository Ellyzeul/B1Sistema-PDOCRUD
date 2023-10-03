<?php namespace App\Actions\Tracking;

use Illuminate\Support\Facades\DB;
use App\Actions\Tracking\DeliveryMethods\Correios;
use App\Actions\Tracking\DeliveryMethods\DHL;
use App\Actions\Tracking\DeliveryMethods\FedEx;
use App\Actions\Tracking\DeliveryMethods\Jadlog;
use App\Actions\Tracking\DeliveryMethods\MercadoLivre;
use App\Actions\Tracking\DeliveryMethods\EnviaDotcom;

class UpdateOrInsertOrderTrackingAction
{
    public function handle(string $trackingCode, string | null $deliveryMethod)
    {
        return $this->updateOrInsertOrderTracking($trackingCode, $deliveryMethod);
    }

    private function updateOrInsertOrderTracking(string $trackingCode, string | null $deliveryMethod)
	{
		if(!isset($this->supportedServices[$deliveryMethod])) return ["Serviço não suportado", 400];

		$response = null;
		if($deliveryMethod == "Correios") $response = (new Correios())->fetch($trackingCode);
		if($deliveryMethod == "Jadlog") $response = (new Jadlog())->fetch($trackingCode);
		if($deliveryMethod == "FedEx") $response = (new FedEx())->fetch($trackingCode);
		if($deliveryMethod == "Envia.com") $response = (new EnviaDotcom())->fetch($trackingCode);
		
		if($deliveryMethod == "Mercado Livre") {
			$data = $this->getOrderIdAndcompanyId($trackingCode);
			if(!isset($data)) return ["Erro na leitura dos dados", 500]; 
			$response = (new MercadoLivre($data->id_company))->fetch($data->online_order_number);
		} 

		if($deliveryMethod == "DHL"){
			$DHL = new DHL();
			$response = $DHL->fetchDHLMyAPI($trackingCode);

			$response = 
				$response === []
					? $DHL->fetchDHLShipmentTrackingUnified($trackingCode) 
					: $response;
		} 

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

    private array $supportedServices = [
		"Correios" => true,
		"Jadlog" => true,
		"DHL" => true,
		"FedEx" => true,
		"Mercado Livre" => true,
		"Envia.com" => true,
	];

	private function getOrderIdAndcompanyId(string $trackingCode)
	{
        $data = DB::table('order_control')
                    ->select('id_company', 'online_order_number')
                    ->where('tracking_code', $trackingCode)
                    ->first();

        return $data;
	}
}