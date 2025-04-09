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
    collect($request->items)->each(fn(array $item) => $this->handleOrder($request, $item));

    return Address::find($request->order_number)->update($request->address);
  }

  private function handleOrder(Request $request, array $orderData)
  {
    $order = Order::find($orderData['id']);

    $order->weight = $orderData['weight'];
    $order->id_delivery_method = $request->address['delivery_method'] === 0
      ? null
      : $request->address['delivery_method'];

    $order->save();
  }
}
