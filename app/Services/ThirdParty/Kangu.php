<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Http;

class Kangu
{
  public function getEtiqueta(string $company, string $code)
  {
    return Http::kangu($company)->get("/imprimir-etiqueta/$code")->object();
  }

  public function postSimular(
    string $company,
    string $zipCodeOrigin,
    string $zipCodeDestination,
    int $value,
    int $weight,
    int $height,
    int $length,
    int $depth,
    array $services,
    string $orderBy,
  )
  {
    return Http::kangu($company)->post('/simular', [
      'cepOrigem'=> $zipCodeOrigin,
      'cepDestino'=> $zipCodeDestination,
      'vlrMerc'=> $value,
      'pesoMerc'=> $weight,
      'volumes'=> [[
        'peso'=> $weight,
        'altura'=> $height,
        'largura'=> $length,
        'comprimento'=> $depth,
        'tipo'=> 'C',
        'valor'=> $value,
        'quantidade'=> 1,
      ]],
      'servicos'=> $services,
      'ordernar'=> $orderBy
    ])->object();
  }
}
