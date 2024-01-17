<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Http;

class Kangu
{
  public function getEtiqueta(string $company, string $code)
  {
    return Http::kangu($company)->get("/imprimir-etiqueta/$code")->object();
  }
}
