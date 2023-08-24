<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingAmazonAction;
use App\Actions\OrderMessage\AskRatingMessage\SendAskRatingEstanteAction;
use App\Actions\OrderMessage\AskRatingMessage\GetAskRatingWhatsappAction;

class OrderMessageService
{
    public function sendAskRating(Request $request)
    {
        $orderId = $request->input('order_id');
        $companyId = $request->input('company_id');        
        $sellerCentral = $request->input('seller_central');
        
        if($sellerCentral === "Amazon-BR" 
            || $sellerCentral === "Amazon-CA" 
            || $sellerCentral === "Amazon-UK"
            || $sellerCentral === "Amazon-US"
        ) return (new SendAskRatingAmazonAction())->handle($orderId); 

        else if($sellerCentral === "Estante-BR")return (new SendAskRatingEstanteAction())->handle($orderId); 
    }

    public function sendAskRatingAmazon(Request $request)
    {
        $orderId = $request->input('order_id');

        return (new SendAskRatingAmazonAction())->handle($orderId);  
    }

    public function sendAskRatingEstante(Request $request)
    {
        $orderId = $request->input('order_id');

        return (new SendAskRatingEstanteAction())->handle($orderId);  
    }    

    public function getAskRatingWhatsapp(Request $request)
    {
        return (new GetAskRatingWhatsappAction())->handle($request);  
    }   
}