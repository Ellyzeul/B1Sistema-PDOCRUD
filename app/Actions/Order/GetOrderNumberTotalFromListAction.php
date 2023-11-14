<?php namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Http\Request;

class GetOrderNumberTotalFromListAction
{
  public function handle(Request $request)
  {
    $orderNumbers = json_decode($request->input('order_numbers_list'));
    $response = [];

    foreach($orderNumbers as $orderNumber) {
      $response[$orderNumber] = Order::where('online_order_number', $orderNumber)->count();
    }

    return $response;
  }
}
