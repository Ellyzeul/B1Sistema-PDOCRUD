<?php

namespace App\Actions\Tracking;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class UpdateAllPurchaseAction
{
  public function handle()
  {
    $deliveryMethods = DB::table('supplier_delivery_methods')->get();

    return Order::select(['supplier_tracking_code', 'id_supplier_delivery_method'])
      ->where('id_phase', '2.31')
      ->get()
      ->map(function (Order $order) use ($deliveryMethods) {
        return (new UpdateOrInsertPurchaseTrackingAction())->handle(
          $order->supplier_tracking_code,
          $deliveryMethods
            ->filter(fn(object $method) => $method->id === $order->id_supplier_delivery_method)
            ->first()
            ->name ?? null
        );
      })
      ->reduce(fn($acc, $cur) => [
        'response' => [...$acc['response'], $cur[0]],
        'code' => $cur[1] === 500 ? 500 : 200
      ], ['response' => [], 'code' => 200]);
  }
}
