<?php namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\PDOCrudService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
	public static function getPDOCrudTable(Request $request)
	{
		return (new PDOCrudService())->getHTML($request);
	}

	public function getShipmentLabelData(Request $request)
	{
		$orderId = $request->input('order_id');

		$order = new Order();
		$response = $order->getShipmentLabelData($orderId);

		return $response;
	}

	public static function importOrdersFromDate(Request $request)
	{
		return (new OrderService())->importOrdersFromDate($request);
	}

	public static function acceptFNACOrder(Request $request)
	{
		return (new OrderService())->acceptFNACOrder($request);
	}

	public static function updateAddressVerified(Request $request)
	{
		return (new OrderService())->updateAddressVerified($request);
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

	public function putBlingOrder(Request $request)
	{
		$order = new Order();

		$blingData = $request->input('bling_data');
		$companyId = $request->input('id_company');
		$response = $order->putBlingOrder($blingData, $companyId);

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

	public static function updateInvoiceNumber(Request $request)
	{
		$orderId = $request->input('order_id');
		$invoiceNumber = $request->input('invoice_number');

		$order = new Order();
		$response = $order->updateInvoiceNumber($orderId, $invoiceNumber);

		return $response;
	}

	public static function getInvoiceLink(Request $request)
	{
		$companyId = $request->input('company_id');
		$blingNumber = $request->input('bling_number');
		
		$order = new Order();
		$response = $order->getInvoiceLink($companyId, $blingNumber);

		return $response;		
	}
}