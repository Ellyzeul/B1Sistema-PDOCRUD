<?php

namespace App\Actions\SupplierPurchase;

use App\Models\Supplier;
use App\Models\SupplierPurchase;
use App\Models\SupplierPurchaseItems;
use Illuminate\Http\Request;

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
    $purchase->freight = $request->freight;
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

      $item->id_purchase = $purchase->id;
      $item->id_order = $itemData['id_order'];
      $item->value = $itemData['value'];

      $item->save();
      $purchase->items->push($item);
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