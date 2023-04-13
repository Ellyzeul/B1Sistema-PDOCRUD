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

	public static function updateReadForShip(Request $request)
	{
		$toUpdate = $request->input("verifieds");
		
		$order = new Order();
		$response = $order->updateReadForShip($toUpdate);

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

	public static function getAddress(Request $request)
	{
		$order = new Order();

		$orderNumber = $request->input('order_number');
		$response = $order->getAddress($orderNumber);

		return $response;
	}

	public static function updateTrackingCode(Request $request)
	{
		$order = new Order();

		$companyId = $request->input('company_id');
		$orderId = $request->input('order_id');
		$blingNumber = $request->input('bling_number');
		$response = $order->updateTrackingCode($companyId, $orderId, $blingNumber);

		return $response;
	}

	public static function updateDeliveryMethod(Request $request)
	{
		$order = new Order();

		$companyId = $request->input('company_id');
		$orderId = $request->input('order_id');
		$blingNumber = $request->input('bling_number');
		$response = $order->updateDeliveryMethod($companyId, $orderId, $blingNumber);

		return $response;
	}

	public function getDataForAskRatingSpreadSheet()
	{
		$order = new Order();

		$result = $order->getDataForAskRatingSpreadSheet();

		return $result;
	}
}