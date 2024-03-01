<?php namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\PDOCrudService;
use App\Services\OrderMessageService;
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

	public static function readOrderControlByOrderNumber(Request $request)
	{
		return (new OrderService())->ReadOrderControlByOrderNumber($request);
	}

	public static function readOrderAddressesByOrderNumber(Request $request)
	{
		return (new OrderService())->ReadOrderAddressesByOrderNumber($request);
	}

	public static function sendOrderToBling(Request $request)
	{
		return (new OrderService())->sendOrderToBling($request);
	}

	public static function acceptFNACOrder(Request $request)
	{
		return (new OrderService())->acceptFNACOrder($request);
	}

	public static function updateAddressVerified(Request $request)
	{
		return (new OrderService())->updateAddressVerified($request);
	}

	public static function updateCancelInvoice(Request $request)
	{
		return (new OrderService())->updateCancelInvoice($request);
	}

	public static function getOrderMessages()
	{
		return (new OrderService())->getOrderMessages();
	}

	public static function postOrderMessage(Request $request)
	{
		return (new OrderService)->postOrderMessage($request);
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

	// public static function sendAskRatingAmazon(Request $request)
	// {
	// 	return (new OrderMessageService())->sendAskRatingAmazon($request);
	// }

	public static function sendAskRatingEstante(Request $request)
	{
		return (new OrderMessageService())->sendAskRatingEstante($request);
	}

	public static function getAskRatingWhatsapp(Request $request)
	{
		return (new OrderMessageService())->getAskRatingWhatsapp($request);
	}

	public static function sendAskRating(Request $request)
	{
		$response = (new OrderMessageService())->sendAskRating($request);

		return $response['success']
			? $response
			: response($response, 400);
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

	public static function updateTrackingService(Request $request)
	{
		$order = new Order();

		$companyId = $request->input('company_id');
		$orderId = $request->input('order_id');
		$blingNumber = $request->input('bling_number');
		$response = $order->updateTrackingService($companyId, $orderId, $blingNumber);

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

	public static function postOrderAddressOnEnviaDotCom(Request $request)
	{
		return (new OrderService())->postOrderAddressOnEnviaDotCom($request);
	}

	public function getOrderNumberTotalFromList(Request $request)
	{
		return (new OrderService())->getOrderNumberTotalFromList($request);
	}

	public function postTrackingCodeOnSellercentral(Request $request)
	{
		return (new OrderService())->postTrackingCodeOnSellercentral($request);
	}

	public function updateDeliveryMethod(Request $request)
	{
		return (new OrderService())->updateDeliveryMethod($request);
	}

	public function sendPreCancellationNotice(Request $request)
	{
		return (new OrderMessageService())->sendPreCancellationNotice($request);
	}

	public function sendCancellationNotice(Request $request)
	{
		return (new OrderMessageService())->sendCancellationNotice($request);
	}
}