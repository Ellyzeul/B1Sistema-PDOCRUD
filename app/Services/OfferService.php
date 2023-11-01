<?php namespace App\Services;

use App\Services\ThirdParty\B1Servicos;
use Illuminate\Http\Request;

class OfferService
{
  public function delete(Request $request)
  {
    return (new B1Servicos())->offerDelete($request->input('isbn'));
  }
}
