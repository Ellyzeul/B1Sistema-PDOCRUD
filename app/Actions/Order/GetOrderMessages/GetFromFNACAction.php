<?php namespace App\Actions\Order\GetOrderMessages;

use App\Services\ThirdParty\FNAC;

class GetFromFNACAction
{
  public function handle()
  {
    return $this->getMessages();
  }

  private function getMessages()
  {
    $fnac = new FNAC('pt', 0);
    
    $orderMessages = $this->fetchOrderMessages($fnac->messagesQuery(messageType: 'ORDER'));
    $offerMessages = $this->fetchOfferMessages($fnac->messagesQuery(messageType: 'OFFER'));

    return array_merge(
      $orderMessages,
      $offerMessages,
    );
  }

  private function fetchOrderMessages(array $xmlMessages)
  {
    $messages = [];

    foreach($xmlMessages as $message) {
      $orderId = "$message->message_referer";
      $messages[$orderId] = isset($messages[$orderId])
        ? array_merge($messages[$orderId], [$message])
        : [$message];
    }
    foreach($messages as $orderId => $chat) {
      $messages[$orderId] = $this->formatResponse($chat, 'order');
    }

    return $messages;
  }

  private function fetchOfferMessages(array $xmlMessages)
  {
    $messages = [];

    foreach($xmlMessages as $message) {
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
