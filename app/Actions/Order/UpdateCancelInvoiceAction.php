<?php namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Http\Request;

class UpdateCancelInvoiceAction
{
  public function handle(Request $request)
  {
    $verifieds = $request->input("verifieds");

    foreach($verifieds as $toUpdate) {
      $id = $toUpdate['id'];
      $cancelledInvoice = $toUpdate['cancel_invoice'];

      Order::where('id', $id)->update(['cancel_invoice' => $cancelledInvoice]);
    }

    return [
      "message" => "Notas fiscais canceladas atualizadas com sucesso."
    ];
  }
}
