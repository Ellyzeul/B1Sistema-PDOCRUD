<?php

namespace App\Services\ThirdParty;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Loggi
{
  private const EXPIRES_IN = 86400;

  public function quotations(string $fromPostalCode, string $toPostalCode, float $weight)
  {
    $response = Http::loggi(token: self::token())->post('/quotations', [
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

    if(!$response->ok() && $response->status() != 404) self::throwError($response->object());

    return collect($response->object()->packagesQuotations[0]->quotations ?? []);
  }

  public function labels(
    string | array $loggiKeys,
    string $format='LABEL_FORMAT_PDF',
    string $layout='LABEL_LAYOUT_A4',
    string $responseType='LABEL_RESPONSE_TYPE_BASE_64',
  )
  {
    $response = Http::loggi(token: self::token())->post('/labels', [
      'loggiKeys' => is_array($loggiKeys) ? $loggiKeys : [$loggiKeys],
      'format' => $format,
      'layout' => $layout,
      'responseType' => $responseType,
    ]);

    if(!$response->ok()) self::throwError($response->object());

    return $response->object()->success;
  }

  public function packageDetails(string $trackingCode)
  {
    $response = Http::loggi(token: self::token())->get("/packages/$trackingCode");

    if(!$response->ok()) self::throwError($response->object());

    return $response->object()->packages[0];
  }

  public function asyncShipment(array $shipmentData)
  {
    $response = Http::loggi(token: self::token())->post('/async-shipments', [
      'shipFrom' => [
        'name' => 'Expedição Seline',
        'federalTaxId' => '26779333000154',
        'address' => ['correiosAddress' => [
          'logradouro' => 'Praça Mariano Melgar, 3',
          'cep' => '02754110',
          'cidade' => 'São Paulo',
          'uf' => 'SP',
        ]],
      ],
      'shipTo' => [
        'name' => $shipmentData['name'],
        'federalTaxId' => $shipmentData['cpf_cnpj'],
        'phoneNumber' => $shipmentData['phone'],
        'address' => [
          'instructions' => $shipmentData['instructions'],
          'correiosAddress' => [
            'logradouro' => $shipmentData['street'],
            'complemento' => $shipmentData['complement'],
            'cep' => $shipmentData['postal_code'],
            'cidade' => $shipmentData['city'],
            'uf' => $shipmentData['uf'],
          ],
        ],
      ],
      'pickupType' => 'PICKUP_TYPE_DROP_OFF',
      'packages' => [[
        'freightType' => 'FREIGHT_TYPE_ECONOMIC',
        'weightG' => $shipmentData['weight'],
        'lengthCm' => $shipmentData['length'],
        'widthCm' => $shipmentData['width'],
        'heightCm' => $shipmentData['height'],
        'documentTypes' => [[
          'contentDeclaration' => [
            'totalValue' => $shipmentData['value'],
            'description' => ' ',
          ]
        ]],
      ]],
    ]);

    Log::debug(json_encode($response->status()));
    if($response->status() !== 202) self::throwError($response->object());

    return $response->object()->packages[0];
  }

  public function tracking(string $trackingCode)
  {
    $response = Http::loggi(token: self::token())->get("/packages/$trackingCode/tracking");

    if(!$response->ok()) self::throwError($response->object());

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
    return Cache::remember('loggi_token', self::EXPIRES_IN, fn() => self::fetchToken());
  }

  private static function fetchToken()
  {
    $response = Http::loggi(auth: true)->post('/oauth2/token', [
      'client_id' => env('LOGGI_CLIENT_ID'),
      'client_secret' => env('LOGGI_CLIENT_SECRET'),
    ])->object();

    if(isset($response->code)) self::throwError($response);

    return $response->idToken;
  }
}
