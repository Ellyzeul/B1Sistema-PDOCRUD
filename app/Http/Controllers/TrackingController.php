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
	
	public function update(Request $request)
	{
		$tracking = new Tracking();
		$trackingCode = $request->input('tracking_code');
		$idDeliveryMethod = $request->input('delivery_method');

		[$response, $statusCode] = $tracking->updateOrInsertTracking($trackingCode, $idDeliveryMethod);

		return response($response, $statusCode);
	}
}
