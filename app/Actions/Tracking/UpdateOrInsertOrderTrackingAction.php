<?php namespace App\Actions\Tracking;

use Illuminate\Support\Facades\DB;
use App\Actions\Tracking\DeliveryMethods\DHL;
use App\Actions\Tracking\DeliveryMethods\FedEx;
use App\Actions\Tracking\DeliveryMethods\Jadlog;
use App\Actions\Tracking\DeliveryMethods\Correios;
use App\Actions\Tracking\DeliveryMethods\Delnext;
use App\Actions\Tracking\DeliveryMethods\EnviaDotCom;
use App\Actions\Tracking\DeliveryMethods\Kangu;
use App\Actions\Tracking\DeliveryMethods\Loggi;
use App\Actions\Tracking\DeliveryMethods\MercadoLivre;
use App\Actions\Tracking\DeliveryMethods\USPS;

class UpdateOrInsertOrderTrackingAction
{
	private array $supportedServices;

	public function __construct()
	{
		$this->supportedServices = [
			'Correios' => fn() => new Correios(),
			'Jadlog' => fn() => new Jadlog(),
			'FedEx' => fn() => new FedEx(),
			'Envia.com' => fn() => new EnviaDotCom(),
			'USPS' => fn() => new USPS(),
			'Kangu' => fn() => new Kangu(),
			'Loggi' => fn() => new Loggi(),
			'Mercado Livre' => fn() => new MercadoLivre(),
			'DHL' => fn() => new DHL(),
			'Delnext' => fn() => new Delnext(),
		];
	}

	public function handle(string $trackingCode, string | null $deliveryMethod)
	{
		return $this->updateOrInsertOrderTracking($trackingCode, $deliveryMethod);
	}

	private function updateOrInsertOrderTracking(string $trackingCode, string | null $deliveryMethod)
	{
		if(!isset($this->supportedServices[$deliveryMethod])) return [
			['error_msg' => "Serviço não suportado"], 400
		];

		$response = $this->supportedServices[$deliveryMethod]()->fetch($trackingCode);

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
}