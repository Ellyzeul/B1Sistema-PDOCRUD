<?php

namespace App\Actions\EmittedInvoice;

use App\Models\Address;
use App\Models\EmittedInvoice;
use App\Models\Order;
use App\Services\ThirdParty\FocusNFE;
use Illuminate\Http\Request;

class CreateDevolutionAction
{
  public function handle(Request $request)
  {
    $invoice = EmittedInvoice::find($request->key);
    $address = Address::find($invoice->order_number);
    $items = $this->getItems($invoice->order_number);
    $api = new FocusNFE();

    $api->create($invoice->company, $invoice->order_number, [
      'cpf' => $address->cpf_cnpj,
      'name' => $address->buyer_name,
      'address' => $address->address_1,
      'number' => $address->address_number,
      'county' => $address->county,
      'city' => $address->city,
      'uf' => $address->state,
      'country' => $address->country,
      'postal_code' => $address->postal_code,
      'phone' => $address->buyer_phone,
      'total_value' => $items->map(fn($item) => floatval($item['value']) * intval($item['quantity']))->reduce(fn($acc, $cur) => $acc + $cur, 0) + floatval($address->freight),
      'freight' => floatval($address->freight),
      'cfop' => $this->getCfop($address),
      'type' => '0',
      'finality' => '4',
      'referenced_key' => $invoice->key,
      'items' => $items,
    ]);

    sleep(5);

    while(true) {
      $response = $api->get($invoice->company, $invoice->order_number)->object();

      if($response->status === 'processando_autorizacao') {
        sleep(3);
        continue;
      }

      if($response->status !== 'autorizado') {
        return $response;
      }

      return $this->handleDatabaseUpdate($invoice);
    }
  }

  private function getItems(string $orderNumber)
  {
    $items = [];

    Order::where('online_order_number', $orderNumber)->get()->each(function(Order $order) use($items) {
      if(isset($items[$order->isbn])) {
        $items[$order->isbn]['quantity']++;

        return;
      }

      $items[$order->isbn] = [
        'value' => $order->selling_price,
        'quantity' => 1,
      ];
    });

    return collect(collect($items)->values());
  }

  private function getCfop(Address $address)
  {
    if($address->state === 'SP') return '1202';

    return '2202';
  }

  private function handleDatabaseUpdate(EmittedInvoice $invoice)
  {
    $invoice->cancelled = 1;
    $invoice->save();

    return ['success' => true];
  }

  private function getFocusBaseUrl(bool $debug = false)
  {
    if($debug) return 'https://homologacao.focusnfe.com.br';

    return 'https://api.focusnfe.com.br';
  }
}