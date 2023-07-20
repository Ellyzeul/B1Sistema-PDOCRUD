<?php namespace App\Actions\Order\SendOrderToBling;

use App\Services\ThirdParty\Bling;
use App\Actions\Order\Traits\SendOrderToBlingCommon;

class PutOrderToBlingAction
{
    use SendOrderToBlingCommon;

    public function handle(array $order, array $client, array $items, int $idCompany, $orderId)
    {
        $bling = new Bling($idCompany, 'v3');
        $idContact = $this->getContactId($client, $idCompany);
        $items = $this->formatItems($items, $idCompany);
        $orderNumber = $bling->getOrderById($orderId)->numero;
        $putOrder = $bling->putOrder($orderId, $this->getOrderRequestBody($order, $client, $items, $idContact, $orderNumber));
    
        return $orderNumber;
    }

    private function formatItems(array $items, int $idCompany)
    {
        $bling = new Bling($idCompany);
        $requestBodyItems = array_map(function($item) use ($idCompany, $bling) {
            $sku = ($idCompany === 1 ? 'B1_' : 'SEL_') . $item['isbn'];
            $response = $bling->getProductByCode($sku);
            $idProduct = isset($response->error)
                ? $bling->postProduct([
                    "nome" => $item['title'],
                    "codigo"=> $sku,
                    "preco" => floatval(str_replace(',', '.', $item['value'])),
                    "tipo" => "P",
                    "situacao" => "A",
                    "formato" => "S",
                    "unidade" => "UN",
                    "gtin" => $item['isbn'],
                    "gtinEmbalagem" => $item['isbn'],
                    "tributacao" => [
                        "origem" => 0,
                        "ncm" => "4901.99.00",
                        "cest"=> "28.064.00"
                    ]
                ])
                : $response->id;

            return [
                "produto" => ["id" => $idProduct],
                "codigo" => $sku,
                "quantidade" => intval($item['quantity']),
                "valor" => floatval(str_replace(',', '.', $item['value'])),
                "descricao" => $item['title'],
            ];
        }, $items);

        return $requestBodyItems;
    }
}