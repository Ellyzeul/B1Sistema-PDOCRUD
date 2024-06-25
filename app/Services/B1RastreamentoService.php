<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class B1RastreamentoService
{
  public function createTracking(Request $request)
  {
    $address = DB::table('order_addresses')
      ->where('online_order_number', $request->order_number)
      ->get(['city', 'country', 'state'])
      ->first();

    if(!isset($address)) return response([
      'error_message' => "Pedido $request->order_number não tem endereço...",
    ], 400);

    $response = Http::b1rastreamento()->post('/tracking', [
      'order_number' => $request->order_number,
      'city' => $address->city,
      'uf' => $address->country === 'BR' ? $address->state : $address->country,
    ])->json();

    if(!isset($response['tracking'])) return response([
      'error_message' => 'Erro na criação de rastreio...',
    ], 500);

    Order::where('online_order_number', $request->order_number)
      ->update(['internal_tracking_code' => $response['tracking']['id']]);
    
    return response($response, 201);
  }

  public function orderPhase(Request $request)
  {
    if(!isset($request->order_number)) return ['error' => '{order_number} não especificado'];

    $order = Order::where('online_order_number', $request->order_number)
      ->get(['id_phase', 'tracking_code'])
      ->first();

    return isset($order->id_phase)
      ? [
        'phase' => floatval($order->id_phase),
        'has_tracking' => isset($order->tracking_code) && strlen($order->tracking_code) > 0,
      ]
      : ['error' => 'Não existe pedido para o {order_number} especificado'];
  }

  public function updateTracking(Request $request)
  {
    $orders = Order::whereIn('online_order_number', $request->order_numbers)
      ->get(['id_phase', 'tracking_code', 'internal_tracking_code']);

    $filtered = $orders->filter(fn(Order $order) => isset($order->internal_tracking_code));

    return Http::b1rastreamento()->put('/tracking', [
      'batch' => $filtered->map(fn(Order $order) => (object) [
        'tracking_code' => $order->internal_tracking_code,
        'phase' => floatval($order->id_phase),
        'has_tracking' => isset($order->tracking_code) && strlen($order->tracking_code) > 0,
      ])->toArray()
    ])->object();
  }
}
