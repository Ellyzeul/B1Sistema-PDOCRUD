<?php namespace App\Actions\Order;

use App\Models\Order;
use App\Services\ThirdParty\Bling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateDeliveryMethodAction
{
  public function handle(Request $request)
  {
    $orderId = \intval($request->input('order_id'));
    $deliveryMethod = $request->input('delivery_method');
    $serviceName = $request->input('service_name');

    try {
      $deliveryMethodId = $this->deliveryMethods[$deliveryMethod]['id'];
    }
    catch(\Exception $_) {
      return [ 'success' => false, 'error_msg' => 'Método de envio desconhecido...' ];
    }

    // Order::where('id', $orderId)
    //   ->update([ 'id_delivery_method' => $deliveryMethodId, 'tracking_code' => $serviceName ]);
    
    return $this->updateOnBling(
      $orderId, 
      $this->deliveryMethods[$deliveryMethod]['bling_aliases'][$serviceName]
    );
    
    return [
      'success' => true,
      'msg' => 'Atualizado com sucesso!'
    ];
  }

  private function updateOnBling(int $orderId, string $blingAlias)
  {
    $order = Order::select('id_company','bling_number')->where('id', $orderId)->first();
    $idCompany = $order->id_company;
    $blingNumber = $order->bling_number;
    $bling = (new Bling($idCompany));

    $blingOrder = $this->clearCurrentDeliveryMethodOnBling(
      $bling,
      $bling->getOrder($blingNumber),
    );

    array_push($blingOrder->transporte->volumes, (object) [ 'servico' => $blingAlias ]);
    $bling->putOrder($blingOrder->id, $blingOrder);

    return $blingOrder;
  }

  private function clearCurrentDeliveryMethodOnBling(Bling $bling, object $blingOrder)
  {
    if(count($blingOrder->transporte->volumes) === 0) return $blingOrder;

    $blingOrder->transporte->volumes = [];
    $bling->putOrder($blingOrder->id, $blingOrder);

    return $bling->getOrderById($blingOrder->id);
  }

  private array $deliveryMethods = [
    'correios' => [
      'id' => 2,
      'bling_aliases' => [
        'Sedex' => 'SEDEX CONTRATO AG',
        'PAC' => 'PAC CONTRATO AG',
      ],
    ],
    'jadlog' => [
      'id' => 5,
      'bling_aliases' => [
        '.Package' => '.Package',
      ],
    ],
    'kangu' => [
      'id' => 11,
      'bling_aliases' => [
        'Kangu - Loggi' => 'kangu_E_18277493000177',
        'Kangu - Rede Sul' => 'kangu_E_27221173000358',
        'Kangu - PAC' => 'kangu_E_99999999000000',
        'Kangu - Sedex' => 'kangu_X_99999999000000',
      ],
    ],
  ];
}
