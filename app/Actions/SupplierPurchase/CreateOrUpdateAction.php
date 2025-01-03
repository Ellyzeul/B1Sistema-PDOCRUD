<?php

namespace App\Actions\SupplierPurchase;

use App\Models\Expense;
use App\Models\Order;
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
    if($this->validateExpenseCreation($request, $purchase)) {
      $this->createExpense($request, $purchase);
    }

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
    $purchase->id_bank = $request->bank_account;
    $purchase->id_payment_method = $request->payment_method;
    $purchase->freight = $request->freight;
    $purchase->date = $request->date;
    $purchase->payment_date = $request->payment_date;
    $purchase->status = $request->status;
    $purchase->observation = $request->observation;
    $purchase->sales_total = $this->salesTotal($request);
    $purchase->id_delivery_address = $request->delivery_address;
    $purchase->tracking_code = $request->supplier_tracking_code;
    $purchase->id_delivery_method = $request->supplier_delivery_method;
    $purchase->order_number = $request->order_number;
    $purchase->reference = $request->reference;

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
    $purchase->items = collect([]);

    foreach($request->items as $itemData) {
      $item = $this->purchaseItem($purchase->id, $itemData['id'] ?? null);

      $item->id_purchase = $purchase->id;
      $item->id_order = $itemData['id_order'];
      $item->value = $itemData['value'];
      $item->status = $itemData['status'];

      $item->save();
      $purchase->items->push($item);
      // Order::where('id', $item->id_order)->update([
      //   'supplier_name' => $purchase->id,
      //   'is_on_purchase' => 1,
      // ]);
    }
  }

  private function purchaseItem(int $idPurchase, int | null $idItem): SupplierPurchaseItems
  {
    if($idItem === null) return new SupplierPurchaseItems();

    return SupplierPurchaseItems::where('id', $idItem)
      ->where('id_purchase', $idPurchase)
      ->first();
  }

  private function validateExpenseCreation(Request $request,SupplierPurchase $purchase)
  {
    if(!isset($request->bank_account) || !isset($request->payment_method)) return false;
    if(Expense::where('supplier_purchase_id', $purchase->id)->exists()) return false;

    return true;
  }

  private function createExpense(Request $request, SupplierPurchase $purchase)
  {
    $expense = new Expense();

    $expense->company_id = $request->id_company;
    $expense->expense_category_id = 1;
    $expense->annotations = $request->observation;
    $expense->supplier = $request->supplier;
    $expense->bank_id = $request->bank_account;
    $expense->payment_method_id = $request->payment_method;
    $expense->due_date = $request->date;
    $expense->payment_date = $request->payment_date;
    $expense->value = $this->salesTotal($request) + $request->freight;
    $expense->status = isset($request->payment_date) ? 'paid' : 'pending';
    $expense->has_match = 0;
    $expense->on_financial = 0;
    $expense->supplier_purchase_id = $purchase->id;

    $expense->save();

    return;
  }
}