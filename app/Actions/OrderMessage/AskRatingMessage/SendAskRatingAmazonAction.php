<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use App\Actions\OrderMessage\Traits\AskRatingMessageCommon;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendAskRatingAmazonAction
{
    use AskRatingMessageCommon;

    private string $sellercentral;

    public function __construct(string $sellercentral)
    {
        $this->sellercentral = $sellercentral;
    }

    public function handle(string $orderId)
    {
        return $this->sendAskRatingEmail($orderId);
    }

    private function sendAskRatingEmail(string $orderId)
    {
        $order = Order::where('id', $orderId)->first();
        $address = DB::table('order_addresses')
            ->where('online_order_number', $order->online_order_number)
            ->first();
    
        if(!isset($order) || !isset($address)) return [
            'success' => false,
            'content' => $this->errorMessage($order, $address, $orderId),
        ];

        $response = Http::b1servicos()->post('/message/ask-rating', [
            'order_number' => $order->online_order_number,
            'client_name' => $address->buyer_name,
            'client_email' => $address->buyer_email,
            'sellercentral' => $this->sellercentral,
        ]);
        
        Order::where('id', $orderId)->increment('ask_rating');

        return $response->success
            ? [["message" => "E-mail enviado com sucesso!"], 200]
            : [["message" => $response->errPayload], 400];
    }

    private function errorMessage(?object $order, ?object $address, string $id)
    {
        if(!isset($order)) return "Pedido de ID $id inexistente no sistema...";
        if(!isset($address)) return "Endereço do pedido $order->online_order_number inexistente no sistema...";
    }
}
