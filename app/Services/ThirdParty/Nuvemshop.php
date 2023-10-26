<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Http;

class Nuvemshop
{
  private object $api;

  public function __construct()
  {
    $this->api = Http::nuvemshop(
      env('NUVEMSHOP_API_TOKEN'),
      env('NUVEMSHOP_SHOP_ID'), 
      env('NUVEMSHOP_APP_NAME'),
      env('NUVEMSHOP_APP_DOMAIN')
    );
  }

  /**
  * Obtém todos os pedidos após aquela data
  * 
  * Endpoint GET /orders
  * 
  * @param (string) fromDate
  */  
  public function getOrdersFromDate(string $fromDate)
  {
    return $this->api->get("/orders?created_at_min=$fromDate")->object();
  }

  /**
  * Obtém o pedido através do id
  * 
  * Endpoint GET /orders/id
  * 
  * @param (string) id
  */ 
  public function getOrderById(string $id)
  {
    return $this->api->get("/orders/$id")->object();
  }
}