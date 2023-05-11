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

	public function updateAll()
	{
		$tracking = new Tracking();

		[$response, $statusCode] = $tracking->updateAll();

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

		$originId = $request->input('origin_id');
		$orderId = $request->input('order_id');
		$deliveryMethod = $request->input('delivery_method');
		$weight = $request->input('weight') ?? null;

		$response = $tracking->consultPriceAndShipping($originId, $orderId, $deliveryMethod, $weight);

		return $response;
	}
}
