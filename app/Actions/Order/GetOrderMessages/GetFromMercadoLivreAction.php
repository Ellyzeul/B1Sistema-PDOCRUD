<?php namespace App\Actions\Order\GetOrderMessages;

use App\Models\Order;
use App\Services\ThirdParty\MercadoLivre;

class GetFromMercadoLivreAction
{
  private const ID_SELLERCENTRAL = 9;
  private const COMPANIES = [
    0 => [ 'name' => 'seline' ], 
    1 => [ 'name' => 'b1' ], 
  ];

  public function handle()
  {
    $responses = [];

    foreach(self::COMPANIES as $idCompany => $_) {
      $orderNumbers = $this->getOrderNumbers(self::ID_SELLERCENTRAL, $idCompany);
      $responses = $responses + $this->getMessages($orderNumbers, $idCompany);
    }

    return $responses;
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

  private function getMessages(array $orderNumbers, int $idCompany)
  {
    $mercadoLivre = new MercadoLivre($idCompany);
    $sellerId = $mercadoLivre->getSellerID();
    $messages = [];

    foreach($orderNumbers as $orderNumber) {
      $response = $mercadoLivre->getMessage($orderNumber);
      if(count($response) === 0) continue;

      $messages[$orderNumber] = [
        'sellercentral' => 'mercado-livre', 
        'company' => self::COMPANIES[$idCompany]['name'], 
        'type' => 'order', 
        'to_answer' => [
          'id' => "/packs/$orderNumber/sellers/$sellerId", 
          'client_id' => $this->getClientId($response, $sellerId)
        ], 
        'messages' => $this->formatResponse($response, $sellerId)
      ];
    }

    return $messages;
  }

  private function formatResponse(array $response, int $sellerId)
  {
    $formatted = [];

    foreach($response as $message) {
      array_push($formatted, [
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
}
