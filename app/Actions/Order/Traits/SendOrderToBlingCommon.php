<?php namespace App\Actions\Order\Traits;

use App\Services\ThirdParty\Bling;

trait SendOrderToBlingCommon
{
    private function getContactId(array $client, int $idCompany)
    {
        $bling = new Bling($idCompany);
        $contact = $bling->getContact([
            'numeroDocumento' => $client['cpf_cnpj'], 
        ]);

        if(!isset($contact->id)) {
            $contact = $bling->postContact($this->getClientRequestBody($client));
        }

        return $contact->id;
    }

    private function getClientRequestBody(array $client)
    {
        return [
            "nome" => $client['name'],
            "codigo" => null,
            "situacao" => "A",
            "numeroDocumento"=> $client['person_type'] === 'E'? null : $client['cpf_cnpj'],
            "telefone"=> null,
            "celular"=> $client['phone'],
            "fantasia"=> null,
            "tipo"=> $client['person_type'],
            "indicadorIe"=> 9,
            "ie"=> null,
            "rg"=> null,
            "orgaoEmissor"=> null,
            "email"=> $client['email'],
            "endereco" => [
                "geral" => [
                    "endereco" => $client['address'],
                    "cep" => $client['postal_code'],
                    "bairro" => $client['county'],
                    "municipio" => $client['city'],
                    "uf" => $client['uf'], 
                    "numero" => $client['number'],
                    "complemento" => $client['complement'], 
                ],
                "cobranca" => [
                    "endereco" => null,
                    "cep" => null,
                    "bairro" => null,
                    "municipio" => null,
                    "uf" => null,
                    "numero" => null,
                    "complemento" => null
                ]
            ],
            "vendedor" => ["id" => null],
            "dadosAdicionais" => [
                "dataNascimento" => null,
                "sexo" => null,
                "naturalidade" => null
            ],
            "financeiro" => [
                "limiteCredito" => 0,
                "condicaoPagamento" => null,
                "categoria" => ["id" => null]
            ],
            "pais" => ["nome" => $client['country']],
            "tiposContato" => [[
                "id" => 1431605062, 
                "descricao" => "Cliente"
            ]],
            "pessoasContato" => [[
                "id" => 1431605062,
                "descricao" => "Cliente"
            ]]
        ];
    }

    private function getOrderRequestBody(array $order, array $client, array $items, string $idContact, int|null $orderNumber)
    {   
        $totalRaw = array_reduce($items, function($carry, $item) {
            return $carry + ($item['valor'] * $item['quantidade']);
        });        

        $total = floatval(number_format($totalRaw 
            + floatval(str_replace(',', '.', $order['freight'])) 
            + floatval(str_replace(',', '.', $order['other_expenses'])) 
            - floatval(str_replace(',', '.', $order['discount'])), 2, '.', ''));

        $freight = floatval(str_replace(',', '.', $order['freight']));

        return [
            "numero" => $orderNumber,
            "numeroLoja" => $order['number'],
            "data" => $order['order_date'],
            "dataPrevista" => $order['expected_date'],
            "contato" => ["id" => $idContact],
            "loja" => ["id"=> $order['id_shop']],
            "outrasDespesas" => floatval(str_replace(",", ".", $order['other_expenses'])),
            "observacoes" => "Nº Pedido Loja: {$order['number']}",
            "observacoesInternas" => null,
            "desconto" => [
                "valor" => floatval(str_replace(",", ".", $order['discount'])),
                "unidade" => "REAL"
            ],
            "itens" => $items,
            "parcelas" => [[
                "dataVencimento" => (string) date('Y-m-d', strtotime('+30 days', strtotime($order['order_date']))),
                "valor" => $total, 
                "observacoes" => null,
                "formaPagamento" => ["id" =>  92896],
            ]],
            "transporte" => [
                "frete" => $freight,
                "etiqueta" => [
                    "nome" => $client['name'],
                    "endereco" => $client['address'],
                    "numero" => $client['number'],
                    "complemento" => $client['complement'],
                    "municipio" => $client['city'],
                    "uf" => $client['uf'],
                    "cep" => $client['postal_code'],
                    "bairro" => $client['county'],
                    "nomePais" => $client['country']
                ],
            ]
        ];
    }
}