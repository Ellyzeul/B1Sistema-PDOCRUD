<?php

namespace App\Actions\SupplierPurchase;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetModalInfoAction
{
  public function handle(Request $_)
  {
    return [
      'payment_methods' => PaymentMethod::all(),
      'bank_accounts' => DB::table('companies_accounts')->get(),
      'delivery_addresses' => DB::table('delivery_addresses')->get(),
      'supplier_delivery_methods' => DB::table('supplier_delivery_methods')->get(),
    ];
  }
}
