<?php namespace App\Actions\Order\ImportOrdersFromDate;

use App\Services\ThirdParty\Nuvemshop;
use App\Actions\Order\Traits\ImportOrdersFromDateCommon;

class ImportFromNuvemshopAction
{
  use ImportOrdersFromDateCommon;

  public function handle(string $fromDate, int $_)
  {
    return $this->getNuvemshopOrdersFromDate($fromDate, new Nuvemshop());
  }

  private function getNuvemshopOrdersFromDate(string $fromDate, Nuvemshop $nuvemshop)
  {
    $orders = $nuvemshop->getOrdersFromDate($fromDate);

    $unregisteredOrders = $this->getUnregisteredOrders($orders, 'id');

    $toInsert = $this->handleItemsAndAddressesSeparation(
      $unregisteredOrders, 
      function($registry) use ($nuvemshop) {
        $order = $nuvemshop->getOrderById($registry->id);
        $address = $order->shipping_address;
      
        return [
          'address' => [
            'online_order_number' => $order->id,
            'buyer_name' => $order->contact_name,
            'buyer_email' => $order->contact_email,
            'buyer_phone' => $order->contact_phone,
            'cpf_cnpj' => $order->contact_identification,
            'recipient_name' => $address->name,
            'address_1' => $address->address,
            'county' => $address->locality,
            'city' => $address->city,
            'postal_code' => $address->zipcode,
            'country' => $address->country,
            'state' => $address->province,
            'ship_phone' => $address->phone,
            'price' => $order->total,
            'freight' => $order->shipping_cost_owner,
          ],
          'items' => $this->handleOrderItems($order),
        ];

    });

    return $this->insertOrder($toInsert);
  }

  private function handleOrderItems($order)
  {
    $items = [];

    foreach($order->products as $item){
      array_push($items, [
        'id_company' => 0,
        'id_sellercentral' => 5,
        'online_order_number' => $order->id,
        'isbn' => explode('_', $item->sku)[1],
        'selling_price' => floatval($item->price) * intval($item->quantity),
        'order_date' => date('Y-m-d', strtotime($order->created_at)),
        'expected_date' => (new \DateTime($order->created_at))->modify("+{$order->shipping_max_days} days")->format('Y-m-d'),
      ]);
    }

    return $items;
  }
}