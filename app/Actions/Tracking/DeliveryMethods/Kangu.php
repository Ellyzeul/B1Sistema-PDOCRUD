<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Actions\Tracking\Traits\DeliveryMethodsCommon;
use App\Services\ThirdParty\Kangu as API;

class Kangu
{
  use DeliveryMethodsCommon;

  const COMPANIES = [
    0 => 'seline',
    1 => 'b1',
  ];

  private object $api;

  public function __construct(?int $idCompany = null)
  {
    if(!isset($idCompany)) return;

    $this->api = new API(self::COMPANIES[$idCompany] ?? 1);
  }

  public function fetch(string $trackingCode)
  {
    $this->setAPIUsingTackingCode($trackingCode);
    $response = $this->api->getRastrear($trackingCode);

    if(strlen($response->error->mensagem) > 0  || !isset($response->historico[0])) return [];

    $lastEvent = $response->historico[0];

    return [
      'status' => $lastEvent->ocorrencia,
			'last_update_date' => $lastEvent->data,
			'details' => $lastEvent->observacao,
    ];
  }

  private function setAPIUsingTackingCode(string $trackingCode)
  {
    if(isset($this->api)) return;

    $orderData = $this->getOrderIdAndcompanyIdByTrackingCode($trackingCode);
    $this->api = new API(self::COMPANIES[$orderData->id_company] ?? 'b1');
  }
}
