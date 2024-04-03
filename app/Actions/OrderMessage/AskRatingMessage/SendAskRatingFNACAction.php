<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use App\Models\Order;
use App\Services\ThirdParty\FNAC;

class SendAskRatingFNACAction
{
  private string $country;
  private int $idCompany;

  public function __construct(string $country, int $idCompany)
  {
    $this->country = $country;
    $this->idCompany = $idCompany;
  }

  public function handle(string $orderId)
  {
    $fnac = new FNAC($this->country, $this->idCompany);
    $orderNumber = Order::select('online_order_number')->where('id', $orderId)->first()->online_order_number;
    $order = $fnac->ordersQuery(ordersId: [ $orderNumber ])[0];

    return [
      'success' => true, 
      'content' => $fnac->messagesUpdate(
        $orderNumber, 
        $this->getMessageBody(
          $this->country, 
          $orderNumber, 
          "$order->client_firstname $order->client_lastname"
        ), 
        'create', 
      )
    ];
  }

  private function getMessageBody(string $country, string $orderNumber, string $clientName)
  {
    return __(
      'fnac.ask_rating',
      [ 'clientName' => $clientName, 'orderNumber' => $orderNumber ],
      $country,
    );
  }
}
