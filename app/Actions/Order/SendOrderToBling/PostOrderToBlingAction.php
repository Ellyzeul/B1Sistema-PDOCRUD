<?php namespace App\Actions\Order\SendOrderToBling;

use App\Services\ThirdParty\Bling;
use App\Actions\Order\Traits\SendOrderToBlingCommon;

class PostOrderToBlingAction
{
    use SendOrderToBlingCommon;

    public function handle(array $order, array $client, int $idCompany)
    {
        $bling = new Bling($idCompany);

        $idContact = $this->getContactId($client, $idCompany);

        $order = $bling->postOrder($this->getOrderRequestBody($order, $client, $this->emptyItem, $idContact, null));

        return $order;
    }
    
    private array $emptyItem = [[
        "id" => "",
        "codigo" => null,
        "quantidade" => 1,
        "valor" => 0,
        "descricao" => null,
    ]];
}