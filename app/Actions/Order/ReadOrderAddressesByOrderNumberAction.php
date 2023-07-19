<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReadOrderAddressesByOrderNumberAction
{
    public function handle(Request $request)
    {
        $OrderNumber = $request->input('order_number');

        $response = $this->searchByOrderNumber($OrderNumber);

        return $response;
    }

    private function searchByOrderNumber(string $OrderNumber)
    {
        $data = DB::table("order_addresses")
                    ->select("*")
                    ->where("online_order_number", "=", $OrderNumber)
                    ->first();
                    // ->toArray(); 

        return $data;
    }
}