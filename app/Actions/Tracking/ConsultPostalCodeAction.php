<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Actions\Tracking\DeliveryMethods\Correios;
use App\Actions\Tracking\DeliveryMethods\EnviaDotCom;

class ConsultPostalCodeAction
{
    public function handle(Request $request)
    {
        $postalCode = Str::of($request->input('zip_code'))
            ->replaceMatches("/[^0-9]/", '')
            ->padLeft(8, '0');

        return $this->consultPostalCode($postalCode);
    }

	private function consultPostalCode(string $postalCode)
	{
        try {
            return (new Correios())->consultPostalCode($postalCode);
        }
        catch(\Exception) {
            return (new EnviaDotCom())->validateAddress($postalCode);
        }
	}    
}