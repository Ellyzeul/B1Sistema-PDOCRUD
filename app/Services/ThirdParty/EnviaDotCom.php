<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Http;

class EnviaDotCom
{
  private object $api;

  public function __construct()
  {
    $this->api = Http::enviaDotCom(env('ENVIA_DOT_COM_API_TOKEN'));
  }

  public function getShipment(string $trackingNumber)
  {
    return $this->api->get("/guide/$trackingNumber")->json();
  }

  public function postUserAddress(array $address)
  {
    $validation = $this->validateAddressBody($address);
    if(!$validation['success']) return ['missing_fields' => $validation['missing']];

    return $this->api->post('/user-address', $address)->json();
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

    return Http::envia()->post('/ship/rate', $body)->json();
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
          "length" => $weight === 0.25 ? 20 : 18,
          "width" => $weight === 0.25 ? 18 : 18,
          "height" => $weight === 0.25 ? 1 : 3
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
