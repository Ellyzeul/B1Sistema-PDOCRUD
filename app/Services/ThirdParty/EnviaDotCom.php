<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EnviaDotCom
{
  private static function token() {
    return Cache::rememberForever('envia-api-token', fn() => env('ENVIA_DOT_COM_API_TOKEN'));
  }

  public function getShipment(string $trackingNumber)
  {
    return Http::enviaDotCom(token: EnviaDotCom::token(), scope: 'queries')
      ->get("/guide/$trackingNumber")->json();
  }

  public function getAddressValidation(string $postalCode)
  {
    return Http::enviaDotCom(token: EnviaDotCom::token(), scope: 'geocodes')
      ->get("/zipcode/BR/$postalCode")->object()[0];
  }

  public function postUserAddress(array $address)
  {
    $validation = $this->validateAddressBody($address);
    if(!$validation['success']) return ['missing_fields' => $validation['missing']];

    return Http::enviaDotCom(token: EnviaDotCom::token(), scope: 'queries')
      ->post('/user-address', $address)->json();
  }

  private function validateAddressBody(array $address)
  {
    $fields = ['type','category_id','name','company','email','phone','street','number','district','city','state','country','postal_code','reference'];
    $missing = [];

    foreach($fields as $field) {
      if(!isset($address[$field])) array_push($missing, $field);
    }

    return [
      'success' => count($missing) === 0, 
      'missing' => $missing
    ];
  }

  public function postQuoteShipment(
    string $senderUF,
    string $senderPostalCode,
    string $clientUF,
    string $clientPostalCode,
    float $weight,
    string $courier,
  )
  {
    $body = $this->getQuoteShipmentBody(
      $senderUF,
      $senderPostalCode,
      $clientUF,
      $clientPostalCode,
      $weight,
      $courier,
    );

    return Http::enviaDotCom(scope: 'queries')
      ->post('/ship/rate', $body)->json();
  }

  private function getQuoteShipmentBody(
    string $senderUF,
    string $senderPostalCode,
    string $clientUF,
    string $clientPostalCode,
    float $weight,
    string $courier,
  )
  {
    return [
      "origin" => [
        "name" => "",
        "phone" => "1130909280",
        "street" => "",
        "number" => "",
        "district" => "",
        "city" => "",
        "state" => $senderUF,
        "category" => 1,
        "country" => "BR",
        "postalCode" => $senderPostalCode,
      ],
      "destination" => [
        "name" => "",
        "street" => "",
        "number" => "",
        "district" => "",
        "city" => "",
        "state" => $clientUF,
        "category" => 1,
        "country" => "BR",
        "postalCode" => $clientPostalCode,
      ],
      "packages" => [[
        "content" => "BOOK",
        "boxCode" => "",
        "amount" => 1,
        "type" => "box",
        "weight" => $weight,
        "insurance" => 0,
        "declaredValue" => 100,
        "weightUnit" => "KG",
        "lengthUnit" => "CM",
        "dimensions" => [
          "length" => $weight <= 0.5 ? 25 : 18,
          "width" => $weight <= 0.5 ? 20 : 18,
          "height" => $weight <= 0.5 ? 2 : 3
        ]
      ]],
      "shipment" => [
        "carrier" => $courier,
        "type" => 1
      ],
      "settings" => [
        "currency" => "BRL",
        "comments" => ""
      ]
    ];
  }
}
