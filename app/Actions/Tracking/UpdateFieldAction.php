<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateFieldAction
{
    public function handle(Request $request)
    {
		$trackingCode = $request->input('tracking_code');
		$field = $request->input('field');
		$value = $request->input('value') ?? "";
		$isPurchases = $request->input('is_purchases');

        return $this->updateField($trackingCode, $field, $value, $isPurchases);
    }

	private function updateField(string $trackingCode, string $field, string $value, bool $isPurchases)
	{
		$table = $isPurchases === true ? 'purchase_trackings' : 'trackings';

		DB::table($table)
			->where('tracking_code', '=', $trackingCode)
			->update([
				$field => $value
			]);
		
		return [
			"message" => "Campo atualizado"
		];
	}    
}
