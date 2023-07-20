<?php namespace App\Actions\Order;

use App\Actions\Order\SendOrderToBling\PostOrderToBlingAction; 
use App\Actions\Order\SendOrderToBling\PutOrderToBlingAction; 

class SendOrderToBlingAction
{
    public function handle(array $order, array $client, array $items, int $idCompany)
    {
        return $this->sendOrderToBling($order , $client, $items, $idCompany);
    }

    private function sendOrderToBling(array $order, array $client, array $items, int $idCompany)
    {
        $post = (new PostOrderToBlingAction())->handle($order, $client, $idCompany);

        if(!isset($post->data->id)) return ['Erro na obtenção do Client ID'];
        
        $orderId = $post->data->id;
        $blingNumber = (new PutOrderToBlingAction())->handle($order, $client, $items, $idCompany, $orderId);

        return [
            "bling_number" => $blingNumber,
        ];
    }
}