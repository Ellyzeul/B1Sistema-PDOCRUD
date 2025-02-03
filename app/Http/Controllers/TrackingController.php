<?php

namespace App\Http\Controllers;

use App\Actions\Tracking\CreateShipmentAction;
use App\Actions\Tracking\UpdateExpectedDeliveryDateAction;
use App\Services\TrackingService;
use Illuminate\Http\Request;

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
		[$response, $statusCode] = (new TrackingService())->updateOrInsertOrderTracking($request);

		return response($response, $statusCode);
	}

	public function updatePurchase(Request $request)
	{
		[$response, $statusCode] = (new TrackingService())->updateOrInsertPurchaseTracking($request);

		return response($response, $statusCode);
	}

	public function updateAll(Request $request)
	{
		[$response, $statusCode] = (new TrackingService())->updateAll($request);

		return response($response, $statusCode);
	}

	public function updateField(Request $request)
	{
		return (new TrackingService())->updateField($request);
	}

	public static function consultPriceAndShipping(Request $request)
	{
		return (new TrackingService())->consultPriceAndShipping($request);
	}

	public function consultPostalCode(Request $request)
	{
		return (new TrackingService())->consultPostalCode($request);
	}

	public static function updateOrderPhase(Request $request)
	{
		return (new TrackingService())->updateOrderPhase($request);
	}

	public static function getEnviaDotComShipmentLabel(Request $request)
	{
		return (new TrackingService())->getEnviaDotComShipmentLabel($request);
	}

	public static function getKanguShipmentLabel(Request $request)
	{
		return (new TrackingService())->getKanguShipmentLabel($request);
	}

	public static function getLoggiShipmentLabel(Request $request)
	{
		return (new TrackingService())->getLoggiShipmentLabel($request);
	}

	public static function createInternal(Request $request)
	{
		return (new TrackingService())->createInternal($request);
	}

	public static function updateInternal(Request $request)
	{
		return (new TrackingService())->updateInternal($request);
	}

	public function updateExpectedDeliveryDate(Request $request)
	{
		return (new UpdateExpectedDeliveryDateAction())->handle($request);
	}

	public function createShipment(Request $request)
	{
		return (new CreateShipmentAction())->handle($request);
	}
}
