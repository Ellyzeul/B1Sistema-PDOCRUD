<?php namespace App\Actions\OrderMessage;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendCancellationNoticeAction
{
  public function handle(Request $request)
  {
    $sendDataResponse = $this->getSendData($request->input('order_id'));
    if(!$sendDataResponse->success) return $sendDataResponse;

    $response = Http::b1servicos()->post('/message/cancellation-notice', $sendDataResponse->content);

    return $response->object();
  }

  private function getSendData(string $orderId): object
  {
    $order = Order::where('id', $orderId)->first();
    $address = DB::table('order_addresses')
      ->where('online_order_number', $order->online_order_number)
      ->first();

    if(!isset($order) || !isset($address)) return (object) [
      'success' => false,
      'content' => $this->errorMessage($order, $address, $orderId),
    ];

    $deliveryMethod = DB::table('delivery_methods')->where('id', $order->id_delivery_method)->first();

    if(!isset($deliveryMethod)) return (object) [
      'success' => false,
      'content' => 'Pedido não tem forma de envio definida',
    ];

    $sellercentral = DB::table('sellercentrals')->where('id', $order->id_sellercentral)->first()->name;

    return (object) [
      'success' => true,
      'content' => [
        'order_number' => $order->online_order_number,
        'client_email' => $address->buyer_email,
        'client_name' => $address->buyer_name,
        'delivery_method' => $deliveryMethod->name,
        'ship_date' => $order->ship_date,
        'expected_date' => $address->expected_date,
        'sellercentral' => $sellercentral,
        'company' => $order->id_company === 0 ? 'seline' : 'b1',
      ]
    ];
  }

  private function errorMessage(?object $order, ?object $address, string $id)
  {
      if(!isset($order)) return "Pedido de ID $id inexistente no sistema...";
      if(!isset($address)) return "Endereço do pedido $order->online_order_number inexistente no sistema...";
  }
}
