<?php

namespace App\Http\Controllers;

use App\Actions\SupplierPurchase\CreateOrUpdateAction;
use App\Models\Order;
use App\Models\SupplierPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierPurchaseController extends Controller
{
  public function read()
  {
    return SupplierPurchase::get();
  }

  public function save(Request $request)
  {
    return (new CreateOrUpdateAction())->handle($request);
  }

  public function orderDetails(Request $request)
  {
    $order = Order::find($request->id_order);

    if(!isset($order)) return response([
      'err_msg' => 'ID de pedido não existe...',
    ], 400);
    Log::debug($order->supplier_name);
    Log::debug(json_encode($request->input()));
    if($order->is_on_purchase === 1 && $order->supplier_name != $request->id_purchase) return response([
      'err_msg' => "Pedido já está na compra $order->supplier_name",
    ], 400);

    return $order;
  }
}
