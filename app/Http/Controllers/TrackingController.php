<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tracking;

class TrackingController extends Controller
{
	public function read()
	{
		$tracking = new Tracking();

		$response = $tracking->read();

		return $response;
	}

	public function readPurchases()
	{
		$tracking = new Tracking();

		$response = $tracking->readPurchases();

		return $response;
	}

	public function readForExcel(Request $request)
	{
		$tracking = new Tracking();

		$orderNumbers = $request->input('order_numbers');

		$response = $tracking->readForExcel($orderNumbers);

		return $response;
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

		$response = $tracking->updateField($trackingCode, $field, $value);

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
}
