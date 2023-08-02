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
    $resourceId = $toAnswer['id'];
    $clientId = $toAnswer['client_id'];
    $messageType = $toAnswer['message_type'];
    $idCompany = self::COMPANIES[$company];

    return $messageType === 'order' 
      ? (new MercadoLivre($idCompany))->postMessage($resourceId, $clientId, $text)
      : (new MercadoLivre($idCompany))->postAnswer($resourceId, $text);
  }
}
