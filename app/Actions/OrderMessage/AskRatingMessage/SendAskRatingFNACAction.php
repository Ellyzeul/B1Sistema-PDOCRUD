<?php namespace App\Actions\OrderMessage\AskRatingMessage;

use App\Models\Order;
use App\Services\ThirdParty\FNAC;

class SendAskRatingFNACAction
{
  private string $country;
  private int $idCompany;

  public function __construct(string $country, int $idCompany)
  {
    $this->country = $country;
    $this->idCompany = $idCompany;
  }

  public function handle(string $orderId)
  {
    $fnac = new FNAC($this->country, $this->idCompany);
    $orderNumber = Order::select('online_order_number')->where('id', $orderId)->first()->online_order_number;
    $order = $fnac->ordersQuery(ordersId: [ $orderNumber ])[0];

    return $fnac->messagesUpdate(
      $orderNumber, 
      $this->getMessageBody(
        $this->country, 
        $orderNumber, 
        "$order->client_firstname $order->client_lastname"
      ), 
      'create'
    );
  }

  private function getMessageBody(string $country, string $orderNumber, string $clientName)
  {
    if($country === 'pt') return (
"Olá $clientName
Ficamos felizes por você já ter recebido sua encomenda $orderNumber. Desejamos-lhe uma ótima leitura!
Agradecemos pela preferência e gostaríamos de solicitar sua gentileza em deixar uma avaliação para nós aqui na FNAC sobre a sua satisfação com o serviço.

Saudações!"
    );

    if($country === 'es') return (
"(PT)
Olá $clientName
Ficamos felizes por você já ter recebido sua encomenda $orderNumber. Desejamos-lhe uma ótima leitura!
Agradecemos pela preferência e gostaríamos de solicitar sua gentileza em deixar uma avaliação para nós aqui na FNAC sobre a sua satisfação com o serviço.
Saudações!

(ES)
Hola $clientName
Nos alegra que ya hayas recibido tu pedido $orderNumber. ¡Le deseamos una gran lectura!
Gracias por elegirnos y nos gustaría pedirle que amablemente nos deje un comentario aquí en FNAC sobre su satisfacción con el servicio.
¡Saludos!"
    );
  }
}
