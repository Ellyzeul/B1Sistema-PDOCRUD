<?php

namespace App\Http\Controllers;
use App\Services\TrackingService;
use Illuminate\Http\Request;
use App\Models\Tracking;

class TrackingController extends Controller
{
    public static function readOrders()
    {
        return (new TrackingService())->readOrders();
    }		

	public static function readPurchases()
    {
        return (new TrackingService())->readPurchases();
    }		

	public static function readForExcel(Request $request)
    {
        return (new TrackingService())->readForExcel($request);
    }	
	
	public function update(Request $request)
	{
		$tracking = new Tracking();
		$trackingCode = $request->input('tracking_code');
		$idDeliveryMethod = $request->input('delivery_method');

		[$response, $statusCode] = $tracking->updateOrInsertTracking($trackingCode, $idDeliveryMethod);

		return response($response, $statusCode);
	}

	public function updatePurchase(Request $request)
	{
		$tracking = new Tracking();
		$trackingCode = $request->input('tracking_code');
		$idDeliveryMethod = $request->input('delivery_method');

		[$response, $statusCode] = $tracking->updateOrInsertPurchaseTracking($trackingCode, $idDeliveryMethod);

		return response($response, $statusCode);
	}

	public function updateAll(Request $request)
	{
		$tracking = new Tracking();
		$isPurchases = $request->input('is_purchases');

		[$response, $statusCode] = $tracking->updateAll($isPurchases);

		return response($response, $statusCode);
	}

	public function updateField(Request $request)
	{
		$tracking = new Tracking();

		$trackingCode = $request->input('tracking_code');
		$field = $request->input('field');
		$value = $request->input('value') ?? "";
		$isPurchases = $request->input('is_purchases');

		$response = $tracking->updateField($trackingCode, $field, $value, $isPurchases);

		return $response;
	}

	public function consultPriceAndShipping(Request $request)
	{
		$tracking = new Tracking();

		$orderId = $request->input('order_id');
		$originId = $request->input('origin_id');
		$clientPostalCode = $request->input('client_postal_code');
		$weight = $request->input('weight') ?? null;

		$response = $tracking->consultPriceAndShipping($originId, $orderId, $clientPostalCode, $weight);

		return $response;
	}

	public function consultZipCode(Request $request)
	{
		$tracking = new Tracking();
		$zipCode = $request->input('zip_code');

		$response = $tracking->consultZipCode($zipCode);

		return $response;
	}

    public static function updateOrderPhaseAction(Request $request)
    {
        return (new TrackingService())->updateOrderPhase($request);
    }	
}
