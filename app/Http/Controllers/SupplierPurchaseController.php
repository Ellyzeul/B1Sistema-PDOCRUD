<?php

namespace App\Http\Controllers;

use App\Actions\SupplierPurchase\CreateOrUpdateAction;
use App\Actions\SupplierPurchase\GetModalInfoAction;
use App\Models\Order;
use App\Models\SupplierPurchase;
use Illuminate\Http\Request;

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
    if($order->is_on_purchase === 1 && is_int($order->supplier_name) && $order->supplier_name != $request->id_purchase) return response([
      'err_msg' => "Pedido já está na compra $order->supplier_name",
    ], 400);

    return $order;
  }

  public function modalInfo(Request $request)
  {
    return (new GetModalInfoAction())->handle($request);
  }
}
