<?php namespace App\Actions\Order\ImportOrdersFromDate;

use App\Services\ThirdParty\FNAC;
use App\Actions\Order\SendOrderToBlingAction;
use App\Actions\Order\Traits\ImportOrdersFromDateCommon;

class ImportFromFNACAction
{
  use ImportOrdersFromDateCommon;

  public function handle(string $fromDate, int $idCompany)
  {
    $fnac = new FNAC(0);

    $unregisteredOrders = $this->getUnregisteredOrders($fnac->ordersQuery(
      fromDate: $fromDate, 
      dateType: 'CreatedAt', 
      states: ['Created'], 
    ), 'order_id');

    $blingNumbers = $this->handleBlingOrderCreate($unregisteredOrders);

    $toInsert = $this->handleItemsAndAddressesSeparation($unregisteredOrders, function($order) use ($blingNumbers): array {
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
          'items' => $this->getFNACOrderItems($order, $blingNumbers), 
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

  private function getFNACOrderItems(object $order, array $blingNumbers)
  {
    $items = [];
    $orderDate = explode('T', $order->created_at)[0];
    foreach($order->order_detail as $item) {
      $quantity = intval("$item->quantity");
      for($i = 0; $i < $quantity; $i++) array_push($items, [
        'id_company' => 0, 
        'id_sellercentral' => 8, 
        'accepted' => (string) $item->state === 'ToAccept' ? 2 : 3, 
        'online_order_number' => "$order->order_id", 
        'bling_number' => $blingNumbers["$order->order_id"], 
        'order_date' => $orderDate, 
        'isbn' => explode('_', $item->offer_seller_id)[1], 
        'selling_price' => "$item->price", 
        'ship_date' => "$order->max_expedition_date", 
        'expected_date' => "$order->max_delivery_date", 
      ]);
    }

    return $items;
  }

  private function handleBlingOrderCreate($orders)
  {
    $requestBody = [];
    foreach($orders as $order){
      $items = [];
      foreach($order->order_detail as $item){
        array_push($items, [
          "title" => "$item->product_name",
          "isbn"  => explode('_', $item->offer_seller_id)[1],
          "value" => "$item->price",
          "quantity" => "$item->quantity"
        ]);
      }
      array_push($requestBody, [
        "id_company" => 0,
        "order" => [
          "number" => "$order->order_id",
          "order_date" => explode('T', $order->created_at)[0],
          "expected_date" => "$order->max_delivery_date",
          "id_shop" => "204374622",
          "other_expenses" => "",
          "discount" => "",
          "freight" => "{$order->order_detail->shipping_price}",
          "total" => ""
        ],
        "client" => [
          "name" => "$order->client_firstname $order->client_lastname ($order->platform_vat_number)",
          "cpf_cnpj" => null,
          "phone" => "{$order->shipping_address->mobile}",
          "person_type" => "E",
          "email" => "$order->client_email",
          "address" => "{$order->shipping_address->address1}",
          "number" => "----",
          "postal_code" => "{$order->shipping_address->zipcode}",
          "uf" => "EX",
          "county" => "----",
          "city" => "{$order->shipping_address->city}",
          "complement" => "{$order->shipping_address->address3}",
          "country" => "PORTUGAL"
        ],
        "items" => $items
      ]);
    }

    $sendOrders = [];
    foreach ($requestBody as $element) {
      $blingNumber = (new SendOrderToBlingAction())->handle(
        $element["order"], 
        $element["client"], 
        $element["items"], 
        $element["id_company"]
      );
      $sendOrders = array_merge(
        $sendOrders, 
        [ $element["order"]["number"] => $blingNumber['bling_number'] ]
      );
    }

    return $sendOrders;
  }
}
