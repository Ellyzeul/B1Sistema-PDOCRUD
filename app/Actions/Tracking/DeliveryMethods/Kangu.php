<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Services\ThirdParty\Kangu as API;

class Kangu
{
  const COMPANIES = [
    0 => 'seline',
    1 => 'b1',
  ];

  private object $api;

  public function __construct(int $idCompany)
  {
    $this->api = new API(self::COMPANIES[$idCompany]);
  }

  public function fetch(string $trackingCode)
  {
    $response = $this->api->getRastrear($trackingCode);

    if(isset($response->error) || !isset($response->historico[0])) return [];

    $lastEvent = $response->historico[0];

    return [
      'status' => $lastEvent['ocorrencia'],
			'last_update_date' => $lastEvent['data'],
			'details' => $lastEvent['observacao'],
    ];
  }
}
