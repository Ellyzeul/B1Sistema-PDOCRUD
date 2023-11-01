<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Http;

class B1Servicos
{
  public function offerDelete(string $isbn)
  {
    $response = Http::b1servicos()->delete('/offer', [ 'isbn' => $isbn ]);

    return $response->object();
  }
}
