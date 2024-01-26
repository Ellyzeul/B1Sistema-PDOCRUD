<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Http;

class Kangu
{
  private object $api;

  public function __construct(string $company)
  {
    $this->api = Http::kangu($company);
  }

  public function getRastrear(string $code)
  {
    return $this->api->get("/rastrear/$code")->object();
  }

  public function getEtiqueta(string $code)
  {
    return $this->api->get("/imprimir-etiqueta/$code")->object();
  }

  public function postSimular(
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
    return $this->api->post('/simular', [
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
