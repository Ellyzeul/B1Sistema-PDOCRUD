<?php

namespace App\Actions\EmittedInvoice;

use App\Services\ThirdParty\FocusNFE;
use App\Models\EmittedInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateAction
{
  public function handle(Request $request)
  {
    $orderNumber = $request->input('order_number');

    if(EmittedInvoice::where('order_number', $orderNumber)->exists()) {
      return [
        'status' => 'duplicado',
        'status_sefaz' => '-1',
        'mensagem_sefaz' => "Uma nota relativa ao pedido $orderNumber já existe...",
      ];
    }
    
    return $this->handleFocusRequest($request);
  }

  private function handleFocusRequest(Request $request)
  {
    $address = $request->input('address');
    $items = collect($request->input('items'));
    $orderNumber = $request->input('order_number');
    $company = $request->input('company');
    $date = date('Y-m-d H:i:s');
    $api = new FocusNFE();

    $createResponse = $api->create($company, $orderNumber, [
      'cpf' => $address['cpf_cnpj'],
      'name' => $address['buyer_name'],
      'address' => $address['address_1'],
      'number' => $address['address_number'],
      'county' => $address['county'],
      'city' => $address['city'],
      'uf' => $address['state'],
      'country' => $address['country'],
      'postal_code' => $address['postal_code'],
      'phone' => $address['buyer_phone'],
      'total_value' => $items->map(fn($item) => $item['value'])->reduce(fn($acc, $cur) => $acc + $cur, 0) + floatval($address['freight']),
      'freight' => floatval($address['freight']),
      'items' => $items,
    ])->object();

    Log::debug(json_encode($createResponse));
    if(isset($createResponse->codigo)) {
      return [
        'status' => 'erro_validacao',
        'mensagem' => $createResponse->mensagem . (
          isset($createResponse->erros)
            ? "\n\n".collect($createResponse->erros)->map(fn($error) => '- ' . $error->campo . ': ' . $error->mensagem)->join('\n')
            : ''
          ),
      ];
    }

    sleep(5);

    while(true) {
      $response = $api->get($company, $orderNumber)->object();

      if($response->status === 'processando_autorizacao') {
        sleep(3);
        continue;
      }

      return $this->handleDatabaseInsert($response, $date, $company);
    }
  }

  private function handleDatabaseInsert(object $invoice, string $date, string $company)
  {
    return (new EmittedInvoice())->insert([
      'key' => explode('NFe', $invoice->chave_nfe)[1],
      'number' => intval($invoice->numero),
      'emitted_at' => $date,
      'order_number' => $invoice->ref,
      'company' => $company,
      'link_danfe' => $this->getFocusBaseUrl(debug: true).$invoice->caminho_danfe,
      'link_xml' => $this->getFocusBaseUrl(debug: true).$invoice->caminho_xml_nota_fiscal,
    ]);
  }

  private function getFocusBaseUrl(bool $debug = false)
  {
    if($debug) return 'https://homologacao.focusnfe.com.br';

    return 'https://api.focusnfe.com.br';
  }
}