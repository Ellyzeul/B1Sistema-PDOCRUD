<?php namespace App\Actions\Order\ImportOrdersFromDate;

use App\Services\ThirdParty\FNAC;
use App\Actions\Order\Traits\ImportOrdersFromDateCommon;

class ImportFromFNACAction
{
  use ImportOrdersFromDateCommon;

  public function handle(string $fromDate, int $idCompany)
  {
    $fnac = new FNAC();

    $unregisteredOrders = $this->getUnregisteredOrders($fnac->ordersQuery(
      fromDate: $fromDate, 
      dateType: 'CreatedAt', 
      states: ['Created']
    ), 'order_id');

    $toInsert = $this->handleItemsAndAddressesSeparation($unregisteredOrders, function($order): array {
      $shippingAddress = $order->shipping_address;
      $freight = $this->getFNACOrderFreight($order);

      return [
        'address' => [
          'online_order_number' => "$order->order_id", 
          'buyer_name' => "$order->client_firstname $order->client_lastname ($order->platform_vat_number)", 
          'buyer_email' => "$order->client_email", 
          'recipient_name' => "$shippingAddress->firstname $shippingAddress->lastname", 
          'address_1' => "$shippingAddress->address1", 
          'address_2' => "$shippingAddress->address2", 
          'address_3' => "$shippingAddress->address3", 
          'postal_code' => "$shippingAddress->zipcode", 
          'city' => "$shippingAddress->city", 
          'country' => "$shippingAddress->country", 
          'buyer_phone' => "$shippingAddress->phone", 
          'ship_phone' => "$shippingAddress->mobile", 
          'freight' => $freight
        ], 
          'items' => $this->getFNACOrderItems($order), 
      ];
    });

    return $this->insertOrder($toInsert);
  }

  private function getFNACOrderFreight(object $order): float
  {
    $freights = [];
    foreach($order->order_detail as $item) {
      array_push($freights, $item->shipping_price);
    }

    return array_reduce($freights, fn($acc, $cur) => $acc + $cur, 0);
  }

  private function getFNACOrderItems(object $order)
  {
    $items = [];
    $orderDate = explode('T', $order->created_at)[0];
    foreach($order->order_detail as $item) {
      array_push($items, [
        'id_company' => 0, 
        'id_sellercentral' => 8, 
        'accepted' => (string) $item->state === 'ToAccept' ? 2 : 3, 
        'online_order_number' => "$order->order_id", 
        'order_date' => $orderDate, 
        'isbn' => explode('_', $item->offer_seller_id)[1], 
        'selling_price' => "$item->price", 
        'ship_date' => "$order->max_expedition_date", 
        'expected_date' => "$order->max_delivery_date", 
      ]);
    }

    return $items;
  }
}
