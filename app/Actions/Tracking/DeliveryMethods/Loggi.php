<?php

namespace App\Actions\Tracking\DeliveryMethods;

use App\Services\ThirdParty\Loggi as API;

class Loggi
{
  private API $client;

  public function __construct()
  {
    $this->client = new API();
  }

  public function quotations(string $fromPostalCode, string $toPostalCode, float $weight)
  {
    $quotations = $this->client->quotations($fromPostalCode, $toPostalCode, $weight);

    return $quotations->map(fn($quotation) => [
      'name' => $quotation->freightTypeLabel,
      'price' => floatval($quotation->price->totalAmount->units . '.' . $quotation->price->totalAmount->nanos),
	    'expected_deadline' => $quotation->sloInDays,
    ]);
  }
}