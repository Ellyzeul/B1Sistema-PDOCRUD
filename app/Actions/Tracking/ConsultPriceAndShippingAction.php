<?php namespace App\Actions\Tracking;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Actions\Tracking\DeliveryMethods\Correios;
use App\Actions\Tracking\DeliveryMethods\EnviaDotCom;
use App\Actions\Tracking\DeliveryMethods\Jadlog;
use App\Actions\Tracking\DeliveryMethods\Loggi;
use App\Services\ThirdParty\Kangu;

class ConsultPriceAndShippingAction
{
	private array $mappers = [];

	public function __construct()
	{
		$this->mappers = [
			'correios' => fn($cotation) => [
				'name' => $cotation->name,
				'delivery_method' => 'correios',
				'price' => $cotation->price,
				'expected_deadline' => $cotation->expected_deadline,
				'expected_date' => $cotation->expected_date,
			],
			'jadlog' => fn($cotation) => [
				'name' => '.Package',
				'delivery_method' => 'jadlog',
				'price' => $cotation->price,
				'expected_deadline' => null,
				'expected_date' => $cotation->expected_deadline,
			],
			'kangu' => fn($cotation) => [
				'name' => "Kangu - {$cotation->transp_nome}",
				'delivery_method' => 'kangu',
				'price' => $cotation->vlrFrete,
				'expected_deadline' => $cotation->prazoEnt,
				'expected_date' => $cotation->dtPrevEnt,
			]
		];
	}

	public function handle(Request $request)
	{
		$orderId = $request->input('order_id');
		$originId = $request->input('origin_id');
		$clientPostalCode = $request->input('client_postal_code');
		$weight = $request->input('weight') ?? 0;        

		return $this->consultPriceAndShipping($orderId, $originId, $clientPostalCode, $weight);
	}

	private function consultPriceAndShipping(string $orderId, string $originId, string $clientPostalCode, float $weight)
	{
		$originZipCode = DB::table('delivery_addresses')
			->select('postal_code')
			->where('id', $originId)
			->first()
			->postal_code;
	
		$responseLoggi = (new Loggi())->quotations($originZipCode, $clientPostalCode, $weight);
		$responseCorreios = (new Correios())->consultPriceAndShipping($originZipCode, $clientPostalCode, $weight);
		$responseJadlog = (new Jadlog())->consultPrice($originZipCode, $clientPostalCode, $weight);
		$responseKangu = (new Kangu('seline'))->postSimular($originZipCode, $clientPostalCode, 100, $weight, 3, 18, 18, ['E' , 'X' , 'M' , 'R'], 'prazo');
		$responseEnvia = (new EnviaDotCom('seline'))->postQuoteShipment($orderId, $originZipCode, $clientPostalCode, $weight);

		return [
			"Loggi" => $responseLoggi,
			"Correios" => $responseCorreios,
			"Jadlog" => $responseJadlog,
			"Kangu" => $responseKangu,
			"Envia" => $responseEnvia,
		];
	}

	private function getFromServices()
	{

	}
}