<?php

namespace App\Services\ThirdParty;

use App\Models\EmittedInvoice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class FocusNFE
{
  private const COMPANY_INFO = [
    'seline' => [
      'cnpj' => '26779333000154',
      'name' => 'RV de Lima Comercio de Livros Ltda',
      'fantasy_name' => 'Livraria Seline',
      'state_registration' => '141692264114',
    ],
    'b1' => [
      'cnpj' => '47317204000114',
      'name' => 'B1 Comercio de Livros e Distribuidora LTDA',
      'fantasy_name' => 'B1 Comércio de Livros',
      'state_registration' => '136548985112',
    ],
    'j1' => [
      'cnpj' => '47317204000114',
      'name' => 'B1 Comercio de Livros e Distribuidora LTDA',
      'fantasy_name' => 'B1 Comércio de Livros',
      'state_registration' => '136548985112',
    ],
    'r1' => [
      'cnpj' => '47317204000114',
      'name' => 'B1 Comercio de Livros e Distribuidora LTDA',
      'fantasy_name' => 'B1 Comércio de Livros',
      'state_registration' => '136548985112',
    ],
    'livrux' => [
      'cnpj' => '47317204000114',
      'name' => 'B1 Comercio de Livros e Distribuidora LTDA',
      'fantasy_name' => 'B1 Comércio de Livros',
      'state_registration' => '136548985112',
    ],
  ];

  public function create(string $company, string $orderNumber, array $data)
  {
    $number = $this->getNumero($company);
    $totalItems = $this->getTotalItems($data['items']);

    return Http::focusNfe($company)->post("/nfe?ref=$orderNumber", [
      "natureza_operacao" => "Venda", 
      "data_emissao" => date('Y-m-d\TH:i:sP'),
      "tipo_documento" => "1", 
      "local_destino" => $this->getLocalDestino($data),
      "finalidade_emissao" => "1", 
      "consumidor_final" => "1", 
      "presenca_comprador" => "9", 
      "indicador_intermediario" => "0", 
      "cnpj_emitente" => self::COMPANY_INFO[$company]['cnpj'], 
      "nome_emitente" => self::COMPANY_INFO[$company]['name'], 
      "nome_fantasia_emitente" => self::COMPANY_INFO[$company]['fantasy_name'], 
      "logradouro_emitente" => "RUA JOSE LUIS DA SILVA GOMES",
      "numero_emitente" => "102",
      "complemento_emitente" => "",
      "bairro_emitente" => "Vila Iório",
      "municipio_emitente" => "São Paulo",
      "uf_emitente" => "SP",
      "cep_emitente" => "02965050",
      "telefone_emitente" => "1130909280",
      "inscricao_estadual_emitente" => self::COMPANY_INFO[$company]['state_registration'],
      "regime_tributario_emitente" => "3",
      "cpf_destinatario" => $data['cpf'],
      "nome_destinatario" => $data['name'],
      "logradouro_destinatario" => $data['address'],
      "numero_destinatario" => $data['number'],
      "bairro_destinatario" => $data['county'],
      "municipio_destinatario" => $data['city'],
      "uf_destinatario" => $data['uf'],
      "cep_destinatario" => $data['postal_code'],
      "telefone_destinatario" => $data['phone'],
      "indicador_inscricao_estadual_destinatario" => "9",
      "valor_original_fatura" => number_format($data['total_value'], 4),
      "valor_desconto_fatura" => 0,
      "valor_liquido_fatura" => number_format($data['total_value'], 4),
      "itens" => $data['items']->map(fn($item, $index) => [
        "numero_item" => "".($index+1),
        "codigo_produto" => $this->getSkuPrefix($company, $data) . $item['isbn'],
        "codigo_barras_comercial" => $item['isbn'],
        "codigo_barras_tributavel" => $item['isbn'],
        "descricao" => "Livro ".$item['isbn'],
        "codigo_ncm" => $data['ncm'] ?? "49019900",
        "cest" => $data['cest'] ?? "2806400",
        "cfop" => $data['cfop'] ?? $this->getCfop($data),
        "unidade_comercial" => "UN",
        "quantidade_comercial" => number_format($item['quantity'], 4),
        "valor_unitario_comercial" => number_format($item['value'], 4),
        "unidade_tributavel" => "UN",
        "quantidade_tributavel" => number_format($item['quantity'], 4),
        "valor_unitario_tributavel" => number_format($item['value'], 4),
        "valor_frete" => number_format(($data['freight'] / $totalItems) * intval($item['quantity']), 4),
        "inclui_no_total" => "1",
        "icms_percentual_partilha" => "100.0000",
        "icms_origem" => "0",
        "icms_situacao_tributaria" => "40",
        "pis_situacao_tributaria" => "07",
        "cofins_situacao_tributaria" => "07",
      ]), 
      "valor_total" => number_format($data['total_value'], 4),
      "modalidade_frete" => "1",
      "valor_frete" => number_format($data['freight'], 4),
      "formas_pagamento" => [[
        "forma_pagamento" => "01",
        "valor_pagamento" => number_format($data['total_value'], 4),
        "tipo_integracao" => null,
      ]], 
      "numero" => $number,
      "numero_fatura" => "$number",
      "valor_original_fatura" => number_format($data['total_value'], 4),
      "valor_desconto_fatura" => 0,
      "valor_liquido_fatura" => number_format($data['total_value'], 4),
      "informacoes_adicionais_contribuinte" => "Pedido de compra Nº $orderNumber", 
      "cnpj_responsavel_tecnico" => self::COMPANY_INFO[$company]['cnpj'], 
      "contato_responsavel_tecnico" => "Roberto Vicente Lima",
      "email_responsavel_tecnico" => "financeiro@biblio1.com.br",
      "telefone_responsavel_tecnico" => "1130909280",
    ]);
  }

  private function getCfop(array $data)
  {
    if($data['uf'] === 'SP') return '5102';
    if($data['country'] !== 'BR') return '7102';

    return '6109';
  }

  private function getTotalItems(Collection $items)
  {
    return $items->map(fn(array $item) => intval($item['quantity']))->reduce(fn($acc, $cur) => $acc + $cur, 0);
  }

  private function getNumero(string $company)
  {
    return (EmittedInvoice::where('company', $company === 'seline' ? 'seline' : 'b1')
      ->orderBy('number', 'desc')
      ->first()
      ->number ?? 0) + 1;
  }

  private function getSkuPrefix(string $company, array $data)
  {
    if($company === 'seline') {
      if($data['country'] !== 'BR') return 'EXPORT_';

      return 'SEL_';
    }
    if($company === 'b1') {
      if(\in_array($data['country'], ['US', 'USA', 'Estados Unidos', 'United States'])) {
        return 'B1USA_';
      }
      if(\in_array($data['country'], ['ES', 'ESP', 'Espanha', 'Spain'])) {
        return 'B1ESP_';
      }
      return 'B1_';
    }
    if($company === 'j1') {
      return 'J1_';
    }
    if($company === 'r1') {
      return 'R1_';
    }
    if($company === 'livrux') {
      return 'LX_';
    }

    return '';
  }

  private function getLocalDestino(array $data)
  {
    if($data['uf'] === 'SP') return '1';
    if($data['country'] !== 'BR') return '3';

    return '2';
  }

  public function get(string $company, string $orderNumber)
  {
    return Http::focusNfe($company)->get("/nfe/$orderNumber");
  }
}