<?php namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use \PDOCrud;

class OrderController extends Controller
{
    public static function read(Request $request)
    {
        $phase = $request->input('phase') 
            ? strval($request->input('phase')) 
            : null;
        $processed = Order::read($phase);
        $response = [
            "html" => $processed
        ];

        return $response;
    }

    public static function updateAddressVerified(Request $request)
    {
        $toUpdate = $request->input("verifieds");
        $response = Order::updateAddressVerified($toUpdate);

        return $response;
    }
}
