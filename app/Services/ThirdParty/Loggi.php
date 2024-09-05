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
      'pickupTypes' => ['PICKUP_TYPE_DROP_OFF'],
      'packages' => [
        'weight' => $weight * 1000,
        'lengthCm' => 21,
        'widthCm' => 21,
        'heightCm' => 3,
      ],
    ]);

    if(!$response->ok() && $response->status() != 404) Loggi::throwError($response->object());

    return collect($response->object()->packagesQuotations[0]->quotations ?? []);
  }

  public function labels(
    string | array $loggiKeys,
    string $format='LABEL_FORMAT_PDF',
    string $layout='LABEL_LAYOUT_A4',
    string $responseType='LABEL_RESPONSE_TYPE_BASE_64',
  )
  {
    $response = Http::loggi(token: Loggi::token())->post('/labels', [
      'loggiKeys' => is_array($loggiKeys) ? $loggiKeys : [$loggiKeys],
      'format' => $format,
      'layout' => $layout,
      'responseType' => $responseType,
    ]);

    if(!$response->ok()) Loggi::throwError($response->object());

    return $response->object()->success;
  }

  public function tracking(string $trackingCode)
  {
    $response = Http::loggi(token: Loggi::token())->get("/packages/$trackingCode/tracking");

    if(!$response->ok()) Loggi::throwError($response->object());

    return $response->object()->packages[0];
  }

  private function quotationAddress(string $postalCode)
  {
    return ['correios' => ['cep' => $postalCode]];
  }

  private static function throwError(object $err)
  {
    throw new Exception(
      'Loggi auth error: '.($err->code ?? 'NO CODE')." - $err->message\n\n" . json_encode($err->details)
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
