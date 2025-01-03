<?php

namespace App\Actions\Invoice;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\SupplierPurchase;
use App\Models\SupplierPurchaseItems;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LinkPurchaseItemsAction
{
  public function handle(Request $request)
  {
    $accessKey = $request->input('access_key');
    $linked = collect($request->input('linked'));

    SupplierPurchaseItems::where('invoice_key', $accessKey)->get()
      ->filter(fn(SupplierPurchaseItems $item) => 
        !in_array($item->id, $linked->map(fn(array $data) => $data['id'])->toArray())
      )
      ->each(function(SupplierPurchaseItems $item) {
        $item->invoice_key = null;

        $item->save();
      });
    
    $linked->each(function(array $data) use ($accessKey) {
      $item = SupplierPurchaseItems::find($data['id']);
      $item->invoice_key = $accessKey;

      $item->save();
    });

    Invoice::find($accessKey)->update(['match' => $request->input('match')]);
    
    Log::debug('aaaa');
    $this->handleExpensesLink($linked);

    return ['success' => true];
  }

  private function handleExpensesLink(Collection $linked)
  {
    Log::debug(json_encode(
      $linked
        ->map(fn(array $data) => SupplierPurchase::find(SupplierPurchaseItems::find($data['id'])->id_purchase))
        ->unique(fn(SupplierPurchase $purchase) => $purchase->id)
        ->filter(fn(SupplierPurchase $purchase) => $purchase->items->map(fn(SupplierPurchaseItems $item) => isset($item->invoice_key))->reduce(fn($acc, $cur) => $acc && $cur, true))
        ->map(fn(SupplierPurchase $purchase) => $purchase->id)
    ));
    $linked
      ->map(fn(array $data) => SupplierPurchase::find(SupplierPurchaseItems::find($data['id'])->id_purchase))
      ->unique(fn(SupplierPurchase $purchase) => $purchase->id)
      ->filter(fn(SupplierPurchase $purchase) => $purchase->items->map(fn(SupplierPurchaseItems $item) => isset($item->invoice_key))->reduce(fn($acc, $cur) => $acc && $cur, true))
      ->each(function(SupplierPurchase $purchase) {
        Log::debug($purchase->id);
        Expense::where('supplier_purchase_id', $purchase->id)->update([
          'has_match' => true,
        ]);
      });
  }
}
