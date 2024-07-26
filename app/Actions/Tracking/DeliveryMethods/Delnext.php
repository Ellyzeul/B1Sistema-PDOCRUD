<?php

namespace App\Actions\Tracking\DeliveryMethods;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Delnext
{
  public function fetch(string $trackingCode)
  {
    $tracking = Http::delnext('PT')->asForm()->post('/TrackingAPI', [
      'tracking_number' => $trackingCode,
    ])->object();

    return $this->formatFetch($tracking);
  }

  private function formatFetch(object $tracking)
  {
    $info = $tracking->ParcelInfo;

    return [
			"status" => $info->CurrentStatus->StatusName,
			"last_update_date" => $this->lastUpdateDate($info),
			"details" => $info->CurrentStatus->StatusDescription,
		];
  }

  private function lastUpdateDate(object $info)
  {
    $lastEvent = $info->History[0];

    return date('Y-m-d', strtotime(str_replace('/', '-', $lastEvent->Date)));
  }
}
