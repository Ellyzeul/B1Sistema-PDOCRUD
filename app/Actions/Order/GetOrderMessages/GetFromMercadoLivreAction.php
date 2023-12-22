<?php namespace App\Actions\Order\GetOrderMessages;

use App\Models\Order;
use App\Services\ThirdParty\MercadoLivre;
use App\Actions\Order\Traits\GetOrderMessagesCommon;

class GetFromMercadoLivreAction
{
  use GetOrderMessagesCommon;
  
  private const ID_SELLERCENTRAL = 9;
  // private const COMPANIES = [
  //   0 => [ 'name' => 'seline' ], 
  //   1 => [ 'name' => 'b1' ], 
  // ];
  private string $idCompany;

  public function __construct(string $idCompany)
  {
    $this->idCompany = $idCompany;
  }

  public function handle()
  {
    // $responses = [];

    // foreach(self::COMPANIES as $idCompany => $_) {
    //   $orderNumbers = $this->getOrderNumbers(self::ID_SELLERCENTRAL, $idCompany);
    //   $response = $this->getMessages($orderNumbers, $idCompany);
    //   $responses = array_merge($responses, $response);
    // }
    $orderNumbers = $this->getOrderNumbers(self::ID_SELLERCENTRAL, $this->idCompany);
    // $responses = array_merge($responses, $this->getMessages($orderNumbers, $this->idCompany));
    return $this->getMessages($orderNumbers, $this->idCompany);

    // return $responses;
  }

  private function getMessages(array $orderNumbers, int $idCompany)
  {
    $mercadoLivre = new MercadoLivre($idCompany);
    $sellerId = $mercadoLivre->getSellerID();
    $messages = [];
    $missingIds = [];

    foreach($orderNumbers as $orderNumber) {
      $response = $mercadoLivre->getMessage($orderNumber);

      if(count($response) === 0) continue;

      $missingIds = array_merge($missingIds,
        $this->verifyMissingMessages($orderNumber, $this->getMessagesIds($response))
      );

      if (!empty($missingIds)){
        $filteredMessages = array_filter($response, function($item) use ($missingIds) {
          return in_array($item->id, $missingIds);
        });

        if (!empty($filteredMessages)) {
          $messages[$orderNumber] = [
            'sellercentral' => 'mercado-livre', 
            'company' => $this->idCompany == 0 ? 'seline' : 'b1', 
            'type' => 'order', 
            'to_answer' => [
              'id' => "/packs/$orderNumber/sellers/$sellerId", 
              'client_id' => $this->getClientId($filteredMessages, $sellerId)
            ], 
            'has_attachments' => $this->hasAttachments($filteredMessages),
            'messages' => $this->formatResponse($filteredMessages, $sellerId),
            'timestamp' => $filteredMessages[0]->message_date->received
          ];      
        }
      }
    }

    foreach($this->getOfferMessagesInfo($mercadoLivre, $idCompany) as [ $offerId, $messageInfo ]) {
      $messages[$offerId] = $messageInfo;
    }

    return $this->writeMessagesInDataBase($messages);
  }

  private function formatResponse(array $response, int $sellerId)
  {
    $formatted = [];

    foreach($response as $message) {
      array_push($formatted, [
        'id' => $message->id,
        'text' => $message->text, 
        'date' => $message->message_date->created, 
        'from' => $message->from->user_id === $sellerId ? 'seller' : 'client'
      ]);
    }

    return $formatted;
  }

  private function getClientId(array $messages, string $sellerId)
  {
    foreach($messages as $message) {
      $userId = $message->from->user_id;
      if($userId !== $sellerId) return $userId;
    }

    return '';
  }

  private function hasAttachments(array $messages)
  {
    foreach($messages as $message) {
      return $message->message_attachments === null
        ? 0
        : 1;
    }
  }

  private function getMessagesIds(array $messages)
  {
    $messagesIds = [];
    foreach($messages as $message) {
      array_push($messagesIds, $message->id);
    }    
    return $messagesIds;
  }

  private function getOfferMessagesInfo(MercadoLivre $mercadoLivre, int $idCompany)
  {
    $offerMessages = [];

    foreach($mercadoLivre->getQuestions() as $offerMessageInfo) {

      array_push($offerMessages, [$offerMessageInfo->id, [
        'sellercentral' => 'mercado-livre', 
        'company' => $this->idCompany == 0 ? 'seline' : 'b1', 
        'type' => 'offer', 
        'to_answer' => [ 'id' => $offerMessageInfo->id ], 
        'messages' => $this->getOfferMessages($offerMessageInfo), 
        'timestamp' => $offerMessageInfo->date_created
        ]]);
      }

    return $offerMessages;
  }

  private function getOfferMessages(object $offerMessageInfo)
  {
    $clientQuestion = [
      'id' => $offerMessageInfo->id,
      'text' => $offerMessageInfo->text, 
      'date' => $offerMessageInfo->date_created, 
      'from' => 'client', 
    ];

    if(!isset($offerMessageInfo->answer)) return [ $clientQuestion ];
    $answer = $offerMessageInfo->answer;

    return [ $clientQuestion, [
      'id' => "{$offerMessageInfo->id}_{$offerMessageInfo->from->id}",
      'text' => $answer->text, 
      'date' => $answer->date_created, 
      'from' => 'seller', 
    ] ];
  }
}
