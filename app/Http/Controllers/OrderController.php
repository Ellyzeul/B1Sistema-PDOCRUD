<?php namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public static function read(Request $request)
    {
        
        $phase = $request->input('phase') 
            ? strval($request->input('phase')) 
            : null;

        $order = new Order();
        $processed = $order->read($phase);
        $response = [
            "html" => $processed
        ];

        return $response;
    }

    public static function updateAddressVerified(Request $request)
    {
        $toUpdate = $request->input("verifieds");
        
        $order = new Order();
        $response = $order->updateAddressVerified($toUpdate);

        return $response;
    }

    public static function getTotalOrdersInPhase()
    {
        $order = new Order();

        $response = $order->getTotalOrdersInPhase();

        return $response;
    }

    public static function sendAskRatingEmail(Request $request)
    {
        $order = new Order();

        $orderId = $request->input('order_id');
        [$response, $statusCode] = $order->sendAskRatingEmail($orderId);

        return response($response, $statusCode);
    }

    public static function getAskRatingWhatsapp(Request $request)
    {
        $order = new Order();
        
        $orderId = $request->input('order_id');
        
        [$response, $statusCode] = $order->getAskRatingWhatsapp($orderId);

        return response($response, $statusCode);
    }    
}
