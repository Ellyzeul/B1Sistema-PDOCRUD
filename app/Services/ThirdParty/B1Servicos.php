<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Http;

class B1Servicos
{
  public function offerDelete(string $isbn)
  {
    $response = Http::b1servicos()->delete('/offer', [ 'isbn' => $isbn ]);

    return $response->object();
  }

  public function orderTrackingCodePost(string $orderNumber, string $sellercentral, string $company, string $trackingNumber, string $shipDate)
  {
    $response = Http::b1servicos()->post('/order/tracking-code', [
      'orderNumber' => $orderNumber,
      'sellercentral' => $sellercentral,
      'company' => $company,
      'trackingNumber' => $trackingNumber,
      'shipDate' => $shipDate,
    ]);

    return $response;
  }
}
