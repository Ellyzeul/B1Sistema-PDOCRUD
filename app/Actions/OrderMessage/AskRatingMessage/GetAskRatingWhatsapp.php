<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use Illuminate\Http\Request;
use App\Actions\OrderMessage\Traits\AskRatingMessageCommon;

class GetAskRatingWhatsapp
{
    use AskRatingMessageCommon;

    public function handle(Request $request)
    {
        $orderId = $request->input('order_id');

        return $this->getAskRatingWhatsapp($orderId);
    }

    private function getAskRatingWhatsapp(string $orderId)
    {
        [ $blingNumber, $apikey, $fromEmail, $companyName, $isNational ] = $this->getMailingInfo($orderId);

        $blingResponse = $this->getBlingMessagingInfo($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        [ $clientName, $clientEmail, $orderNumber, $bookName, $phone ] = $blingResponse;

        return [[
            'formatted_message' => view('whatsapp/ask-rating/national', [
                'orderNumber' => $orderNumber,
                'clientName' => $clientName,
                'bookName' => $bookName,
                'companyName' => $companyName,
            ])->render(),
            'cellphone' => $phone
        ], 200];
    }  
}
