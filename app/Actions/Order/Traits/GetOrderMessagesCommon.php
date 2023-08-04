<?php namespace App\Actions\Order\Traits;

use App\Models\Order;

trait GetOrderMessagesCommon
{
  private function getOrderNumbers(int $idSellercentral, ?int $idCompany)
  {
    $orderNumbers = [];
    $prepare= Order::select('online_order_number')
      ->where('id_sellercentral', $idSellercentral)
      ->where('id_phase', '<', 7);

    if(isset($idCompany)) $prepare->where('id_company', $idCompany);

    $results = $prepare->get();

    foreach($results as $result) {
      array_push($orderNumbers, $result->online_order_number);
    }

    return $orderNumbers;
  }
}
