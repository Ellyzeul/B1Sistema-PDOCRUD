<?php

namespace App\Actions\Tracking;

use App\Services\ThirdParty\Loggi;
use Illuminate\Http\Request;

class GetLoggiShipmentLabelAction
{
  private Loggi $api;

  public function __construct()
  {
    $this->api = new Loggi();
  }

  public function handle(Request $request)
  {
    $tracking = $this->api->tracking($request->tracking_code);
    $label = $this->api->labels($tracking->loggiKey);

    return $label;
  }
}
