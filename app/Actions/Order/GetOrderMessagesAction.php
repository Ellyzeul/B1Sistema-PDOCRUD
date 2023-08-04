<?php namespace App\Actions\Order;

use App\Actions\Order\GetOrderMessages\GetFromFNACAction;
use App\Actions\Order\GetOrderMessages\GetFromMercadoLivreAction;

class GetOrderMessagesAction
{
  public function handle()
  {
    $messagesCollection = [];
    $handlers = [
      [ 'name' => 'MercadoLivre',  'obj' =>new GetFromMercadoLivreAction() ], 
      [ 'name' => 'FNAC',  'obj' =>new GetFromFNACAction() ]
    ];
    $errors = [];
    
    foreach($handlers as $handler) {
      try {
        $messagesCollection = $messagesCollection + $handler['obj']->handle();
      }
      catch(\Exception) {
        array_push($errors, "Erro ao recuperar as mensagens de: {$handler['name']}");
      }
    }

    return $this->mapMessages($messagesCollection);
  }

  private function mapMessages(array $messagesCollection)
  {
    $mapped = [];

    foreach($messagesCollection as $orderNumber => $messagesInfo) {
      $mappedInfo = [
        'sellercentral' => $messagesInfo['sellercentral'], 
        'company' => $messagesInfo['company'], 
        'to_answer' => $messagesInfo['to_answer'], 
        'type' => $messagesInfo['type'], 
        'messages' => $this->sortMessages($messagesInfo['messages']), 
      ];
      $mappedInfo['latest_message'] = $mappedInfo['messages'][0];

      $mapped[$orderNumber] = $mappedInfo;
    }

    return $this->sortMessagesCollection($mapped);
  }

  private function sortMessages(array $messages)
  {
    usort($messages, fn($msg1, $msg2) => $msg1['date'] < $msg2['date'] ? 1 : -1);

    return $messages;
  }

  private function sortMessagesCollection(array $messagesCollection): array
  {
    $sorted = [];
    $sorting = [];

    foreach($messagesCollection as $orderNumber => $messagesInfo) {
      array_push($sorting, [$orderNumber, $messagesInfo]);
    }

    usort(
      $sorting, 
      fn($msg1, $msg2) =>  $msg1[1]['latest_message']['date'] < $msg2[1]['latest_message']['date'] 
        ? 1 
        : -1
    );

    foreach($sorting as [ $orderNumber, $messageInfo ]) {
      $sorted[$orderNumber] = $messageInfo;
    }

    return $sorted;
  }
}
