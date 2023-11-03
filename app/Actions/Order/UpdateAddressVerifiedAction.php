<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use App\Models\Order;

class UpdateAddressVerifiedAction
{
  public function handle(Request $request)
  {
    $verifieds = $request->input("verifieds");

    foreach($verifieds as $toUpdate) {
      $id = $toUpdate['id'];
      $addressVerified = $toUpdate['address_verified'];

      Order::where('id', $id)->update(['address_verified' => $addressVerified]);
    }

    return [
      "message" => "Verificação de endereços atualizada com sucesso."
    ];
}
}
