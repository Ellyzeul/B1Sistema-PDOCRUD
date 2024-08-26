<?php

namespace App\Actions\SupplierPurchase;

use App\Models\Order;
use App\Models\Supplier;
use App\Models\SupplierPurchase;
use App\Models\SupplierPurchaseItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateOrUpdateAction
{
  public function handle(Request $request)
  {
    $purchase = $this->purchase($request);

    $this->savePurchase($purchase, $request);
    $this->saveItems($request, $purchase);

    return response([
      'message' => 'Pedido inserido',
      'purchase' => $purchase,
    ], 201);
  }

  private function purchase(Request $request)
  {
    if(isset($request->id)) return SupplierPurchase::find($request->id);

    return new SupplierPurchase();
  }

  private function savePurchase(SupplierPurchase $purchase, Request $request)
  {
    $purchase->id_company = $request->id_company;
    $purchase->id_supplier = Supplier::idFromName($request->supplier);
    $purchase->purchase_method = $request->purchase_method;
    $purchase->id_payment_method = $request->payment_method;
    $purchase->freight = $request->freight;
    $purchase->date = $request->date;
    $purchase->status = $request->status;
    $purchase->sales_total = $this->salesTotal($request);

    $purchase->save();
  }

  private function salesTotal(Request $request)
  {
    return collect($request->items)
      ->map(fn($item) => $item['value'])
      ->reduce(fn($acc, $cur) => $acc + $cur, 0);
  }

  private function saveItems(Request $request, SupplierPurchase $purchase)
  {
    $purchase->items = [];

    foreach($request->items as $itemData) {
      $item = $this->purchaseItem($purchase->id, $itemData['id'] ?? null);
      Log::debug(json_encode($itemData));

      $item->id_purchase = $purchase->id;
      $item->id_order = $itemData['id_order'];
      $item->value = $itemData['value'];
      $item->status = $itemData['status'];

      $item->save();
      $purchase->items->push($item);
      Order::where('id', $item->id_order)->update([
        'supplier_name' => $purchase->id,
        'is_on_purchase' => 1,
      ]);
    }
  }

  private function purchaseItem(int $idPurchase, int | null $idItem): SupplierPurchaseItems
  {
    if($idItem === null) return new SupplierPurchaseItems();

    return SupplierPurchaseItems::where('id', $idItem)
      ->where('id_purchase', $idPurchase)
      ->first();
  }
}