<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use Illuminate\Http\Request;
use App\Actions\OrderMessage\Traits\AskRatingMessageCommon;

class SendAskRatingAmazon
{
    use AskRatingMessageCommon;

    public function handle(Request $request)
    {
        $orderId = $request->input('order_id');

        return $orderId;
    }

    // private function sendAskRatingEmail(string $orderId)
    // {
    //     [ $blingNumber, $apikey, $fromEmail, $companyName, $isNational ] = Order::getMailingInfo($orderId);

    //     $blingResponse = Order::getBlingMessagingInfo($apikey, $blingNumber);
    //     if(isset($blingResponse['error'])) return $blingResponse['error'];

    //     [ $clientName, $clientEmail, $orderNumber, $bookName ] = $blingResponse;

    //     Mail::to($clientEmail)
    //         ->send(new AskRating(
    //             $fromEmail, 
    //             $isNational,
    //             $clientName, 
    //             $orderNumber, 
    //             $bookName, 
    //             $companyName, 
    //         ));
        
    //     DB::table('order_control')
    //         ->where('id', $orderId)
    //         ->increment('ask_rating');

    //     return [["message" => "E-mail enviado com sucesso!"], 200];
    // }
   
}
