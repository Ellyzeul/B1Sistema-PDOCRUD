<?php namespace App\Actions\Order\ImportOrdersFromDate;

use App\Services\ThirdParty\MercadoLivre;
use App\Actions\Order\Traits\ImportOrdersFromDateCommon;

class ImportFromMercadoLivreAction
{
  use ImportOrdersFromDateCommon;

  public function handle(string $fromDate, int $idCompany)
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
          'items' => $this->handleOrderItems(
            $order->order_items, 
            $order, 
            $shipment, 
            $idCompany, 
            $orderId, 
            $shipping_cost, 
            $mercadoLivre
          )
        ];
    });

    return $this->insertOrder($toInsert);
  }

  private function handleOrderItems(array $items, object $order, object $shipment, int $idCompany, string $orderId, float $shipping_cost, MercadoLivre $mercadoLivre): array
  {
    $mapped = [];

    foreach($items as $item) {
      $quantity = $item->quantity;
      $isbn = isset($item->item->seller_sku)
        ? explode('_', $item->item->seller_sku)[1]
        : $this->getItemISBN($item->item->id, $mercadoLivre);

      for($i = 0; $i < $quantity; $i++) {
        array_push($mapped, [
          'id_company' => $idCompany, 
          'id_sellercentral' => 9, 
          'online_order_number' => $orderId, 
          'order_date' => date('Y-m-d', strtotime($order->date_closed . '-3 hours')), 
          'expected_date' => date('Y-m-d', strtotime($shipment->shipping_option->estimated_schedule_limit->date . '-3 hours')), 
          'isbn' => $isbn, 
          'selling_price' => round($item->full_unit_price - $item->sale_fee - $shipping_cost, 2), 
          'ship_date' => date('Y-m-d H:i:s', strtotime($order->manufacturing_ending_date)),
        ]);
      }
    }

    return $mapped;
  }

  private function getItemISBN(string $id, MercadoLivre $mercadoLivre)
  {
    $item = $mercadoLivre->getItemById($id);
    $filtered = array_filter($item->attributes, fn($attr) => $attr->id === 'GTIN');
    $isbnAttr = array_pop($filtered);

    return $isbnAttr->value_name;
  }
}
