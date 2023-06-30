<?php namespace App\Actions\Order\Traits;

use Illuminate\Support\Facades\DB;
use App\Models\Order;

trait ImportOrdersFromDateCommon
{
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

  private function insertOrder(object $toInsert): array
  {
    Order::insert($toInsert->items);
    DB::table('order_addresses')->insert($toInsert->addresses);

    return $toInsert->items;
  }
}
