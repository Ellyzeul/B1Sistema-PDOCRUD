<?php

namespace App\Actions\Invoice;

use App\Models\SupplierPurchaseItems;
use Illuminate\Http\Request;

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

    return ['success' => true];
  }
}
