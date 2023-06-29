<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Services\ThirdParty\FNAC;
use App\Services\ThirdParty\MercadoLivre;

class ImportOrdersFromDateAction
{
  private array $sellercentrals = [
    ['id_company' => 0, 'channel' => 'mercado-livre'], 
    ['id_company' => 1, 'channel' => 'mercado-livre'], 
    ['id_company' => 0, 'channel' => 'fnac'], 
  ];

  public function handle(Request $request)
  {
    $fromDate = $request->input('from');
    $response = [];

    foreach($this->sellercentrals as $sellercentral) {
      $response = array_merge($response, $this->importFromSellercentral(
        $fromDate, 
        $sellercentral['id_company'], 
        $sellercentral['channel'], 
      ));
    }

    return $response;
  }

  private function importFromSellercentral(string $fromDate, int $idCompany, string $channel): array
  {
    if($channel === 'mercado-livre') return $this->importFromMercadoLivre($fromDate, $idCompany);
    if($channel === 'fnac') return $this->importFromFNAC($fromDate, $idCompany);

    throw new \Exception("Sales channel unknown: $channel");
  }

  private function importFromMercadoLivre(string $fromDate, int $idCompany): array
  {
    $mercadoLivre = new MercadoLivre($idCompany);
    $orders = $mercadoLivre->getOrdersBySearch(dateCreatedFrom: $fromDate . "T00:00:00")->results;

    $unregisteredOrders = $this->getUnregisteredOrders(array_map(
      fn($order) => $order->payments[0], 
      $orders
    ), 'order_id');

    $toInsert = $this->handleItemsAndAddressesSeparation(
      $unregisteredOrders, 
      function($registry) use($mercadoLivre, $idCompany) {
        $orderId = $registry->order_id;
        $order = $mercadoLivre->getOrderById($orderId);
        $shipment = $mercadoLivre->getShipment($order->shipping->id);
        $shipping_cost = $shipment->shipping_option->list_cost - $shipment->shipping_option->cost;
        $receiver = $shipment->receiver_address;

        return [
          'address' => [
            'online_order_number' => $orderId, 
            'buyer_name' => $order->buyer->first_name . ' ' . $order->buyer->last_name, 
            'recipient_name' => $receiver->receiver_name, 
            'address_1' => $receiver->street_name . ', ' . $receiver->street_number, 
            'address_2' => $receiver->comment, 
            'county' => $receiver->neighborhood->name, 
            'city' => $receiver->city->name, 
            'country' => $receiver->country->id, 
            'state' => $receiver->state->name, 
            'ship_phone' => $receiver->receiver_phone, 
          ], 
          'items' => array_map(fn($item) => [
            'id_company' => $idCompany, 
            'id_sellercentral' => 9, 
            'online_order_number' => $orderId, 
            'order_date' => date('Y-m-d', strtotime($order->date_closed . '-3 hours')), 
            'expected_date' => date('Y-m-d', strtotime($shipment->shipping_option->estimated_schedule_limit->date . '-3 hours')), 
            'isbn' => explode('_', $item->item->seller_sku)[1], 
            'selling_price' => round($item->full_unit_price - $item->sale_fee - $shipping_cost, 2), 
            'ship_date' => date('Y-m-d H:i:s', strtotime($order->manufacturing_ending_date)),
          ], $order->order_items)
        ];
    });

    Order::insert($toInsert->items);
    DB::table('order_addresses')->insert($toInsert->addresses);

    return $toInsert->items;
  }

  private function importFromFNAC(string $fromDate, int $idCompany): array
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

    Order::insert($toInsert->items);
    DB::table('order_addresses')->insert($toInsert->addresses);

    return $toInsert->items;
  }

  private function getUnregisteredOrders(array $orders, string $idPropertyPath): array
  {
    $propsUntilId = explode('.', $idPropertyPath);
    $orderIDs = array_map(fn($order) => $this->getOrderId($order, $propsUntilId), $orders);

    $regiteredIDs = array_map(
      fn($registry) => $registry['online_order_number'], 
      Order::whereIn('online_order_number', $orderIDs)
        ->select('online_order_number')
        ->get()
        ->toArray()
    );

    return array_filter(
      $orders, 
      fn($order) => !\in_array($this->getOrderId($order, $propsUntilId), $regiteredIDs)
    );
  }

  private function getOrderId(object $order, array $propsUntilId): string | int
  {
    $id = null;

    foreach($propsUntilId as $prop) {
      $values = \get_object_vars($order);
      $id = $values[$prop];
    }

    if(\in_array(gettype($id), ['array', 'object', 'NULL'])) {
      throw new \Exception("Order ID not found on path {\join('.', $propsUntilId)}");
    }

    return $id;
  }

  private function handleItemsAndAddressesSeparation(array $toInsert, callable $handleMap): object
  {
    $formatted = array_map($handleMap, $toInsert);

    $items = array_reduce(array_map(fn($registry) => $registry['items'], $formatted), 
      fn($acc, $cur) => array_merge($acc, $cur), 
      []
    );
    $addresses = array_map(fn($registry) => $registry['address'], $formatted);

    return (object) [
      'items' => $items, 
      'addresses' => $addresses
    ];
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
