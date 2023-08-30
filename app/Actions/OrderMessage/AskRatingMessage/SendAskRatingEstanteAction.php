<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use App\Actions\OrderMessage\Traits\AskRatingMessageCommon;
use Illuminate\Support\Facades\DB;
use App\Mail\AskRatingEstante;

class SendAskRatingEstanteAction
{
    use AskRatingMessageCommon;

    public function handle(string $orderId)
    {
        return $this->sendAskRatingEmail($orderId);
    }

    private function sendAskRatingEmail(string $orderId)
    {
        [ $blingNumber, $apikey, $fromEmail, $companyName, $isNational ] = $this->getMailingInfo($orderId);

        $orderData = $this->getOrderData($orderId);

        $content = new AskRatingEstante(
            $fromEmail, 
            $isNational,
            $orderData["clientName"], 
            $orderData["orderNumber"], 
            $companyName
        );

        $this->sendEmail($orderData["clientEmail"], $content);

        DB::table('order_control')
            ->where('id', $orderId)
            ->increment('ask_rating');

        return [["message" => "E-mail enviado com sucesso!"], 200];
    }

    private function getOrderData(string $orderId)
    {
        $orderNumber = DB::table('order_control')
                    ->select('online_order_number')
                    ->where('id', '=', $orderId)
                    ->first()->online_order_number;
        
        $clientData = DB::table('order_addresses')
            ->select('buyer_name', 'buyer_email')
            ->where('online_order_number', '=', $orderNumber)
            ->first();

        return [
            'orderNumber' => $orderNumber,
            'clientName' => $clientData->buyer_name,
            'clientEmail' => $clientData->buyer_email
        ];
    }
}
