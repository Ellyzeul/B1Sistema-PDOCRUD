<?php namespace App\Actions\Order\PostOrderMessage;

use App\Services\ThirdParty\MercadoLivre;

class PostMercadoLivreMessageAction
{
  private const COMPANIES = [
    'seline' => 0, 
    'b1' => 1, 
  ];

  public function handle(string $text, string $company, array $toAnswer)
  {
    $messageType = $toAnswer['message_type'];
    $idCompany = self::COMPANIES[$company];

    return $messageType === 'order' 
      ? $this->handleOrderMessage($text, $idCompany, $toAnswer)
      : $this->handleOfferMessage($text, $idCompany, $toAnswer);
  }

  private function handleOrderMessage(string $text, int $idCompany, array $toAnswer)
  {
    $resourceId = $toAnswer['id'];
    $clientId = $toAnswer['client_id'];

    return (new MercadoLivre($idCompany))->postMessage($resourceId, $clientId, $text);
  }

  private function handleOfferMessage(string $text, int $idCompany, array $toAnswer)
  {
    $questionId = $toAnswer['id'];

    return (new MercadoLivre($idCompany))->postAnswer($questionId, $text);
  }
}
