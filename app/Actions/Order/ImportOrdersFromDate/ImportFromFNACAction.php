<?php namespace App\Actions\Order\ImportOrdersFromDate;

use App\Services\ThirdParty\FNAC;
use App\Actions\Order\SendOrderToBlingAction;
use App\Actions\Order\Traits\ImportOrdersFromDateCommon;

class ImportFromFNACAction
{
  use ImportOrdersFromDateCommon;

  private array $sellercentralFromCountry = [ 'pt' => 8, 'es' => 11 ];

  public function handle(string $fromDate, int $idCompany)
  {
    $response = [];

    $response = array_merge($response, $this->handleFNAC($fromDate, 'pt', new FNAC('pt', 0)));
    $response = array_merge($response, $this->handleFNAC($fromDate, 'es', new FNAC('es', 0)));

    return $response;
  }

  private function handleFNAC(string $fromDate, string $country, FNAC $fnac)
  {
    $unregisteredOrders = $this->getUnregisteredOrders($fnac->ordersQuery(
      fromDate: $fromDate, 
      dateType: 'CreatedAt', 
      states: ['Created'], 
    ), 'order_id');

    $blingNumbers = $this->handleBlingOrderCreate($unregisteredOrders);

    $toInsert = $this->handleItemsAndAddressesSeparation($unregisteredOrders, function($order) use ($blingNumbers, $country): array {
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
          'items' => $this->getFNACOrderItems($order, $blingNumbers, $country), 
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

  private function getFNACOrderItems(object $order, array $blingNumbers, string $country)
  {
    $items = [];
    $orderDate = explode('T', $order->created_at)[0];
    foreach($order->order_detail as $item) {
      $quantity = intval("$item->quantity");
      for($i = 0; $i < $quantity; $i++) array_push($items, [
        'id_company' => 0, 
        'id_sellercentral' => $this->sellercentralFromCountry[$country], 
        'accepted' => (string) $item->state === 'ToAccept' ? 2 : 3, 
        'online_order_number' => "$order->order_id", 
        'bling_number' => $blingNumbers["$order->order_id"], 
        'order_date' => $orderDate, 
        'isbn' => $this->getISBN($item), 
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
          "isbn"  => $this->getISBN($item),
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
  
  private function getISBN(object $item)
  {
    return str_starts_with($item->offer_seller_id, 'SEL_')
      ? explode('_', $item->offer_seller_id)[1]
      : $item->offer_seller_id;
  }
}