<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Services\ThirdParty\USPS as API;

class USPS
{
  private API $api;
  public function __construct()
  {
    $this->api = new API();
  }

  public function fetch(string $trackingCode)
  {
    $response = $this->api->getTracking($trackingCode);
    $lastEvent = $response->trackingEvents[0];

    return [
			"status" => $lastEvent->eventType,
			"last_update_date" => date('Y-m-d', strtotime($lastEvent->eventTimestamp)),
			"details" => $response->statusSummary,
    ];
  }
}
