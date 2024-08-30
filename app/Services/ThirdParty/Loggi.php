<?php

namespace App\Services\ThirdParty;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Loggi
{
  private const EXPIRES_IN = 86400;

  public function quotations(string $fromPostalCode, string $toPostalCode, float $weight)
  {
    $response = Http::loggi(token: Loggi::token())->post('/quotations', [
      'shipFrom' => $this->quotationAddress($fromPostalCode),
      'shipTo' => $this->quotationAddress($toPostalCode),
      'packages' => [
        'weight' => $weight * 1000,
        'lengthCm' => 21,
        'widthCm' => 21,
        'heightCm' => 3,
      ]
    ]);

    if(!$response->ok() && $response->status() != 404) Loggi::throwError($response->object());

    return collect($response->object()->packagesQuotations[0]->quotations ?? []);
  }

  private function quotationAddress(string $postalCode)
  {
    return ['correios' => ['cep' => $postalCode]];
  }

  private static function throwError(object $err)
  {
    throw new Exception(
      "Loggi auth error: $err->code - $err->message\n\n" . json_encode($err->details)
    );
  }

  private static function token()
  {
    return Cache::remember('loggi_token', Loggi::EXPIRES_IN, fn() => Loggi::fetchToken());
  }

  private static function fetchToken()
  {
    $response = Http::loggi(auth: true)->post('/oauth2/token', [
      'client_id' => env('LOGGI_CLIENT_ID'),
      'client_secret' => env('LOGGI_CLIENT_SECRET'),
    ])->object();

    if(isset($response->code)) Loggi::throwError($response);

    return $response->idToken;
  }
}
