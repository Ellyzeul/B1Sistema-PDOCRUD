<?php namespace App\Actions\Tracking\DeliveryMethods;

use Illuminate\Support\Facades\Http;

class DHL
{
	public function fetchDHLMyAPI(string $trackingCode)
	{
		$response = Http::withHeaders(["Accept-Language" => "pt-BR"])
			->withBasicAuth(env('DHL_API_CLIENT_ID'), env('DHL_API_CLIENT_SECRET'))
			->get("https://express.api.dhl.com/mydhlapi/shipments/$trackingCode/tracking");

		if(!isset($response) || !isset($response['shipments'])) return [];
		
		$events = $response['shipments'][0]['events'];
		$last = sizeof($events)-1;

		$lastUpdateDate = $response['shipments'][0]['events'][$last]['date'];
		$lastUpdateDescription = "{$response['shipments'][0]['events'][$last]['description']}\n";
		$deliveryExpectedDate = $response['shipments'][0]['estimatedDeliveryDate'] ?? null;

		if($last>=1){
			$penultimate = $last-1;
			$penultimateUpdateDate = $response['shipments'][0]['events'][$penultimate]['date'] ?? "";
			$penultimateUpdateDescription = "{$response['shipments'][0]['events'][$penultimate]['description']}\n" ?? "";

			if($penultimate>=1){
				$antepenultimate = $penultimate-1;
				$antepenultimateUpdateDate = $response['shipments'][0]['events'][$antepenultimate]['date'] ?? "";
				$antepenultimateUpdateDescription = "{$response['shipments'][0]['events'][$antepenultimate]['description']}\n" ?? "";
			}
		}

		$toReturn = [
			"status" => $lastUpdateDescription,
			"last_update_date" => date('Y-m-d', strtotime($lastUpdateDate)),
			"details" => date('d/m/Y', strtotime($lastUpdateDate)) .' '. $lastUpdateDescription
						.date('d/m/Y', strtotime($penultimateUpdateDate)) .' '. $penultimateUpdateDescription
						.date('d/m/Y', strtotime($antepenultimateUpdateDate)) .' '. $antepenultimateUpdateDescription
		];

		$toReturn['delivery_expected_date'] = 
			isset($deliveryExpectedDate) 
				? date('Y-m-d', strtotime($deliveryExpectedDate)) 
				: null;
		
		return $toReturn;
	}

	public function fetchDHLShipmentTrackingUnified(string $trackingCode)
	{
		$response = Http::withHeaders(["DHL-API-Key" => env('DHL_API_KEY')])
			->get("https://api-eu.dhl.com/track/shipments?trackingNumber=$trackingCode&language=pt");
		
		if(!isset($response['shipments'][0]['events'][0])) return [];

		$lastEvent = $response['shipments'][0]['events'][0];
		$status = $lastEvent['description'];
		$lastUpdateDate = $lastEvent['timestamp'];
		$deliveryExpectedDate = $response['shipments'][0]['estimatedTimeOfDelivery'] ?? null;
		$details = ($lastEvent['remark'] ?? "") . " " . ($lastEvent['nextSteps'] ?? "");

		$toReturn = [
			"status" => $status,
			"last_update_date" => date('Y-m-d', strtotime($lastUpdateDate)),
			"details" => $details
		];
		isset($deliveryExpectedDate) ? $toReturn['delivery_expected_date'] = $deliveryExpectedDate : null;

		return $toReturn;
	}

}