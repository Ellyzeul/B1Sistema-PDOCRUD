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

        $blingResponse = $this->getBlingMessagingInfo($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        [ $clientName, $clientEmail, $orderNumber, $bookName ] = $blingResponse;

        $content = new AskRatingEstante(
            $fromEmail, 
            $isNational,
            $clientName, 
            $orderNumber, 
            $bookName, 
            $companyName, 
        );

        $this->sendEmail($clientEmail, $content);
                
        DB::table('order_control')
            ->where('id', $orderId)
            ->increment('ask_rating');

        return [["message" => "E-mail enviado com sucesso!"], 200];
    }
}
