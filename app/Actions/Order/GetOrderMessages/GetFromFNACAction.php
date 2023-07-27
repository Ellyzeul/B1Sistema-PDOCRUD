<?php namespace App\Actions\Order\GetOrderMessages;

use App\Models\Order;
use App\Services\ThirdParty\FNAC;
use SimpleXMLElement;

class GetFromFNACAction
{
  private const ID_SELLERCENTRAL = 8;

  public function handle()
  {
    $orderNumbers = $this->getOrderNumbers(self::ID_SELLERCENTRAL);
    return $this->getMessages($orderNumbers);
  }

  private function getOrderNumbers(int $idSellercentral)
  {
    $orderNumbers = [];
    $results = Order::select('online_order_number')
      ->where('id_sellercentral', $idSellercentral)
      ->where('id_phase', '<', 7)
      ->get();

    foreach($results as $result) {
      array_push($orderNumbers, $result->online_order_number);
    }

    return $orderNumbers;
  }

  private function getMessages(array $orderNumbers)
  {
    $fnac = new FNAC();
    $messages = [];

    foreach($orderNumbers as $orderNumber) {
      $response = $fnac->messagesQuery($orderNumber);
      if(count($response) === 0) continue;

      $messages[$orderNumber] = [
        'sellercentral' => 'fnac', 
        'company' => 'seline', 
        'to_answer' => [
          'id' => $this->getLatestClientMessageID($response)
        ], 
        'messages' => $this->formatResponse($response)
      ];
    }

    return $messages;
  }

  private function formatResponse(array $response)
  {
    $formatted = [];

    foreach($response as $message) {
      array_push($formatted, [
        'text' => "$message->message_description", 
        'date' => "$message->created_at", 
        'from' => "{$message->message_from->attributes()->type}" === 'SELLER' 
          ? 'seller' 
          : 'client', 
      ]);
    }

    return $formatted;
  }

  private function getLatestClientMessageID(array $messages)
  {
    $latestMessage = array_reduce(
      $messages, 
      function($acc, $cur) {
        $createdAt = isset($acc) ? "$acc->created_at" : "";
        return $createdAt > "$cur->created_at" ? $acc : $cur;
      }
    );

    return "$latestMessage->message_id";
  }
}
