<?php namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Http\Request;

class UpdateDeliveryMethodAction
{
  public function handle(Request $request)
  {
    $orderId = $request->input('order_id');
    $deliveryMethod = $request->input('delivery_method');
    $serviceName = $request->input('service_name');

    try {
      $deliveryMethodId = $this->deliveryMethodsIds[$deliveryMethod];
    }
    catch(\Exception $_) {
      return [ 'success' => false, 'error_msg' => 'Método de envio desconhecido...' ];
    }

    Order::where('id', $orderId)
      ->update([ 'id_delivery_method' => $deliveryMethodId, 'tracking_code' => $serviceName ]);
    
    return [
      'success' => true,
      'msg' => 'Atualizado com sucesso!'
    ];
  }

  private array $deliveryMethodsIds = [
    'correios' => 2,
    'jadlog' => 5,
    'kangu' => 11,
  ];
}
