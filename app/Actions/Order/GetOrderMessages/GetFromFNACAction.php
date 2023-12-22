<?php namespace App\Actions\Order\GetOrderMessages;

use App\Models\Order;
use App\Services\ThirdParty\FNAC;
use App\Actions\Order\Traits\GetOrderMessagesCommon;

class GetFromFNACAction
{
  use GetOrderMessagesCommon;

  private const ID_SELLERCENTRAL = 8;

  private string $idCompany;
  private string $region;

  public function __construct(string $region, string $idCompany)
  {
    $this->idCompany = $idCompany;
    $this->region = $region;
  }

  public function handle()
  {
    // return ["Ainda está em manutenção"];
    $orderNumbers = ['0FT5KW6VP01XS', '1P8NWPKWJUGPE'];
    // $orderNumbers = $this->getOrderNumbers(self::ID_SELLERCENTRAL);
    var_dump($orderNumbers);
    // $this->getMessages($orderNumbers);
    return $this->getMessages($orderNumbers);
  }

  private function getMessages(array $orderNumbers)
  {
    // $fnac = new FNAC('pt', 0);
    $fnac = new FNAC($this->region, $this->idCompany);
    $messages = [];
    var_dump("teste");
    foreach($orderNumbers as $orderNumber) {
      $response = $fnac->messagesQuery(orderId: $orderNumber);
      if(count($response) === 0) continue;
      // var_dump($orderNumber, $response);
      $messages[$orderNumber] = $this->formatResponse($response, 'order');
      var_dump("msgs", $messages);
      // die();
    }
      
    $response = $fnac->messagesQuery(orderId: $orderNumber);
    // var_dump($response);
    // die();
    $offerMessages = $fnac->messagesQuery(messageType: 'OFFER');

    foreach($offerMessages as $message) {
      $formatted = $this->formatResponse(
        $this->handleOfferMessage($message), 
        'offer', 
      );
      $messageId = explode('-', $formatted['to_answer']['id'])[0];
      $messages[$messageId] = $formatted;
    }

    // var_dump($messages, $offerMessages);
    return $this->writeMessagesInDataBase($messages);
    // die();    
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

    var_dump('last', $latestMessage);
    // die();
    return [
      'sellercentral' => 'fnac', 
      'company' => 'seline', 
      'type' => $type,
      'to_answer' => [
        'id' => "$latestMessage->message_id",
        'client_id' => '',
      ], 
      'has_attachments' => '',
      'messages' => $this->formatMessages($response),
      'timestamp' => "$latestMessage->created_at",
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
