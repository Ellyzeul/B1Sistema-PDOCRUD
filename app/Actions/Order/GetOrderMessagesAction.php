<?php namespace App\Actions\Order;

use App\Actions\Order\GetOrderMessages\GetFromFNACAction;
use App\Actions\Order\GetOrderMessages\GetFromMercadoLivreAction;

class GetOrderMessagesAction
{
  public function handle()
  {
    $messages = [];
    $handlers = [
      [ 'name' => 'mercado-livre',  'obj' =>new GetFromMercadoLivreAction() ], 
      [ 'name' => 'fnac',  'obj' =>new GetFromFNACAction ()]
    ];
    $errors = [];
    
    foreach($handlers as $handler) {
      try {
        $messages = $messages + $handler['obj']->handle();
      }
      catch(\Exception $_) {
        array_push($errors, $handler['name']);
      }
    }

    return $messages;
  }

  private function mapMessages(array $messages)
  {
    
  }
}
