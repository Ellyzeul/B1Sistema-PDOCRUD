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
}
