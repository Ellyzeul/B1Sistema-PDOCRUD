<?php namespace App\Actions\Order\GetOrderMessages;

use App\Models\Order;
use App\Services\ThirdParty\FNAC;

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
      ->whereRaw('order_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()')
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
      $response = $fnac->messagesQuery(orderId: $orderNumber);
      if(count($response) === 0) continue;
      $messages[$orderNumber] = $this->formatResponse($response, 'order');
    }
    $offerMessages = $fnac->messagesQuery(messageType: 'OFFER');

    foreach($offerMessages as $message) {
      $formatted = $this->formatResponse(
        $this->handleOfferMessage($message), 
        'offer', 
      );
      $messageId = explode('-', $formatted['to_answer']['id'])[0];
      $messages[$messageId] = $formatted;
    }

    return $messages;
  }

  private function handleOfferMessage(\SimpleXMLElement $offerMessage): array
  {
    if(!isset($offerMessage->answer)) return [ $offerMessage ];
    $id = "$offerMessage->message_id";
    $description = "$offerMessage->answer";
    $createdAt = "$offerMessage->answer_at";

    return [ $offerMessage, simplexml_load_string(<<<XML
      <message>
        <message_id>$id</message_id>
        <message_description>$description</message_description>
        <message_from type="SELLER" />
        <created_at>$createdAt</created_at>
      </message>
    XML, \SimpleXMLElement::class, LIBXML_NOCDATA) ];
  }

  private function formatResponse(array $response, string $type): array
  {
    $latestMessage = $this->getLatestMessage($response);

    return [
      'sellercentral' => 'fnac', 
      'company' => 'seline', 
      'type' => $type,
      'to_answer' => [
        'id' => "$latestMessage->message_id"
      ], 
      'messages' => $this->formatMessages($response)
    ];
  }

  private function formatMessages(array $messages): array
  {
    $formatted = [];

    foreach($messages as $message) {
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

  private function getLatestMessage(array $messages)
  {
    $latestMessage = array_reduce(
      $messages, 
      function($acc, $cur) {
        $createdAt = isset($acc) ? "$acc->created_at" : "";
        return $createdAt > "$cur->created_at" ? $acc : $cur;
      }
    );

    return $latestMessage;
  }
}
