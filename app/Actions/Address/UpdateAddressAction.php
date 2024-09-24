<?php

namespace App\Actions\Address;

use App\Actions\Tracking\CreateShipmentAction;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;

class UpdateAddressAction
{
  public function handle(Request $request)
  {
    $order = Order::find($request->order_id);

    if(isset($order)) $this->handleOrder($request, $order);

    return Address::find($request->order_number)->update($request->address);
  }

  private function handleOrder(Request $request, Order $order)
  {
    $order->weight = $request->address['weight'];
    $order->id_delivery_method = $request->address['delivery_method'] === 0
      ? null
      : $request->address['delivery_method'];
    
    if(!isset($order->tracking_code) || $order->tracking_code === '') {
      $trackingCode = (new CreateShipmentAction())->handle($request);

      if($trackingCode !== null) $order->tracking_code = $trackingCode;
    }

    $order->save();
  }
}
