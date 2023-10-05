<?php namespace App\Actions\Tracking;

use App\Models\Order;
use App\Services\ThirdParty\EnviaDotCom;
use Illuminate\Http\Request;

class GetEnviaDotComShipmentLabelAction
{
  public function handle(Request $request)
  {
    $orderId = $request->input('order_id');
    $trackingCode = Order::select('tracking_code')
      ->where('id', $orderId)
      ->first()
      ->tracking_code;

    $response = (new EnviaDotCom())->getShipment($trackingCode);

    return [
      'link' => $response['data'][0]['label_file'], 
    ];
  }
}
