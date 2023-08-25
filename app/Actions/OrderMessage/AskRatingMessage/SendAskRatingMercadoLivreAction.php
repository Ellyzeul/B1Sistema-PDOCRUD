<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use App\Actions\OrderMessage\Traits\AskRatingMessageCommon;
use App\Services\ThirdParty\MercadoLivre;
use Illuminate\Support\Facades\DB;

class SendAskRatingMercadoLivreAction
{
    private int $idCompany;
    use AskRatingMessageCommon;
  
    public function __construct(int $idCompany)
    {
      $this->idCompany = $idCompany;
    }
  
    public function handle(string $orderId)
    {
        return $this->sendAskRating($orderId);
    }

    private function sendAskRating(string $orderId)
    {
        $orderNumber = $this->getOrderNumber($orderId)->online_order_number;

        $mercadoLivre = new MercadoLivre($this->idCompany);

        $orderData = $mercadoLivre->getOrderById($orderNumber);
        $clientData = $orderData->buyer;
        $sellerId = $orderData->seller->id;
        $clientName = $clientData->first_name. " ".$clientData->last_name;

        $msg = $this->buildMessage($orderNumber, $clientName);
        
        $resourceId = "/packs/$orderNumber/sellers/$sellerId?tag=post_sale";

        return $mercadoLivre->postMessage($resourceId,$clientData->id, $msg);
    }

    private function buildMessage(string $orderNumber, $clientName)
    {
        return view('/ask-rating/mercado-livre/national', [
            'orderNumber' => $orderNumber,
            'clientName' => $clientName,
        ])->render();
    }

    private function getOrderNumber(string $orderId)
    {
        return DB::table('order_control')
                    ->select('online_order_number')
                    ->where('id', '=', $orderId)
                    ->first();
    }
}