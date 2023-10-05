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
    $fields = ['type','category_id','name','company','email','phone','street','number','district','city','state','country','postal_code','reference',];
    $missing = [];

    foreach($fields as $field) {
      if(!isset($address[$field])) array_push($missing, $field);
    }

    return [
      'success' => count($missing) === 0, 
      'missing' => $missing
    ];
  }
}
