<?php namespace App\Actions\Order;

use App\Services\ThirdParty\EnviaDotCom;
use Illuminate\Http\Request;

class PostOrderAddressOnEnviaDotComAction
{
  public function handle(Request $request)
  {
    $address = $request->input('address');

    return (new EnviaDotCom())->postUserAddress($address);
  }
}
