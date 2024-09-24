<?php

namespace App\Actions\Tracking;

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
          'street' => $address['address_1'],
          'complement' => Str::substr($address['address_1'] . ' ' . $address['delivery_instructions'], 0, 128),
          'city' => $address['city'],
          'uf' => $address['state'],
          'postal_code' => $address['postal_code'],
          'phone' => $address['ship_phone'],
          'cpf_cnpj' => $address['cpf_cnpj'],
          'weight' => intval($address['weight']) * 1000,
          'length' => $address['length'],
          'width' => $address['width'],
          'height' => $address['height'],
          'value' => Str::replace(',', '.', $address['price']),
        ]);
    
        return $response->trackingCode;
      }
    ];
  }

  public function handle(Request $request)
  {
    $deliveryMethod = $request->address['delivery_method'];
    Log::debug($deliveryMethod);

    if(!isset($this->handlers[$deliveryMethod])) return null;

    return $this->handlers[$deliveryMethod]($request);
  }

}