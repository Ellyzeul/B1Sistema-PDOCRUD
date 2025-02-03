<?php

namespace App\Actions\Tracking;

use App\Models\Tracking;
use Illuminate\Http\Request;

class UpdateExpectedDeliveryDateAction
{
  public function handle(Request $request)
  {
    $trackingCode = $request->input('tracking_code');
    $expectedDeliveryDate = $request->input('expected_delivery_date');

    $tracking = Tracking::where('tracking_code', $trackingCode)->get()->first();
    $tracking->delivery_expected_date = $expectedDeliveryDate;

    return [
      'success' => $tracking->save(),
    ];
  }
}