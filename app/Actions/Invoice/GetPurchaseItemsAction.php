<?php

namespace App\Actions\Invoice;

use App\Models\SupplierPurchaseItems;
use Illuminate\Http\Request;

class GetPurchaseItemsAction
{
  public function handle(Request $request)
  {
    $accessKey = $request->input('access_key');
    if($accessKey === null) return response([
      'message' => 'Chave de acesso não foi provida...',
    ], 400);

    return [
      'not_linked' => SupplierPurchaseItems::where('invoice_key', null)->get(),
      'linked' => SupplierPurchaseItems::where('invoice_key', $accessKey)->get(),
    ];
  }
}
