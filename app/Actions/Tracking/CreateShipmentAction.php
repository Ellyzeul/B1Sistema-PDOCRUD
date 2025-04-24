<?php

namespace App\Actions\Tracking;

use App\Models\Order;
use App\Services\ThirdParty\Loggi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateShipmentAction
{
  private const FROM_ADDRESSES = [
    '02965050' => [
      'street' => 'Rua José Luis da Silva Gomes, 102',
      'city' => 'São Paulo',
      'uf' => 'SP',
    ],
    '02754110' => [
      'street' => 'Praça Mariano Melgar, 3',
      'city' => 'São Paulo',
      'uf' => 'SP',
    ]
  ];

  private array $handlers;

  public function __construct()
  {
    $this->handlers = [
      12 => function (Request $request) {
        $address = $request->address;
        $response = (new Loggi())->asyncShipment([
          'name' => $address['recipient_name'],
          'street' => $address['address_1'] . ' ' . $address['address_number'],
          'complement' => Str::substr($address['address_2'], 0, 128),
          'city' => $address['city'],
          'uf' => $address['state'],
          'instructions' => $address['delivery_instructions'],
          'postal_code' => $address['postal_code'],
          'phone' => $address['ship_phone'],
          'cpf_cnpj' => $address['cpf_cnpj'],
          'weight' => intval($request->weight) * 1000,
          'length' => $address['length'],
          'width' => $address['width'],
          'height' => $address['height'],
          'value' => Str::replace(',', '.', $request->price),
        ]);
    
        return $response->trackingCode;
      }
    ];
  }

  public function handle(Request $request)
  {
    $deliveryMethod = $request->address['delivery_method'];

    if(!isset($this->handlers[$deliveryMethod])) return null;
    $trackingCode = $this->handlers[$deliveryMethod]($request);

    $this->updateOrder(Order::where('id', $request->order_id)->first(), $trackingCode);

    return [
      'tracking_code' => $trackingCode,
    ];
  }

  private function updateOrder(?Order $order, string $trackingCode)
  {
    if(!isset($order)) return;

    $order->tracking_code = $trackingCode;

    $order->save();
  }
}