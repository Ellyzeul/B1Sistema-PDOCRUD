<?php namespace App\Actions\Tracking;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Actions\Tracking\DeliveryMethods\Correios;
use App\Actions\Tracking\DeliveryMethods\Jadlog;
use App\Services\ThirdParty\Kangu;

class ConsultPriceAndShippingAction
{
    public function handle(Request $request)
    {
			$orderId = $request->input('order_id');
			$originId = $request->input('origin_id');
			$clientPostalCode = $request->input('client_postal_code');
			$weight = $request->input('weight') ?? null;        

      return $this->consultPriceAndShipping($originId, $orderId, $clientPostalCode, $weight);
    }

    private function consultPriceAndShipping(string $originId, string $orderId, string $clientPostalCode, float | null $weight)
	{

		$originZipCode = DB::table('delivery_addresses')
			->select('postal_code')
			->where('id', $originId)
			->first()
			->postal_code;
		
		$responseCorreios = (new Correios())->consultPriceAndShipping($originZipCode, $clientPostalCode, $weight);
		$responseJadlog = (new Jadlog())->consultPrice($originZipCode, $clientPostalCode, $weight);
		$responseKangu = (new Kangu())->postSimular('seline', $originZipCode, $clientPostalCode, 100, $weight, 3, 18, 18, ['E' , 'X' , 'M' , 'R'], 'prazo');

		return [
			"Correios" => $responseCorreios,
			"Jadlog" => $responseJadlog,
			"Kangu" => $responseKangu,
		];
	}
}