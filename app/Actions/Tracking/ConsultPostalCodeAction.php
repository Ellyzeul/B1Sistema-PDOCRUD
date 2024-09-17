<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use App\Actions\Tracking\DeliveryMethods\Correios;

class ConsultPostalCodeAction
{
    public function handle(Request $request)
    {
        $postalCode = $request->input('zip_code');

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