<?php namespace App\Actions\OrderMessage;

use App\Actions\OrderMessage\Traits\OrderMessageCommon;
use App\Actions\Tracking\UpdateOrInsertOrderTrackingAction;
use App\Models\Order;
use App\Services\ThirdParty\EnviaDotCom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendTrackingUpdateAction
{
  use OrderMessageCommon;

  private const ENVIA_ID = 8;
  public function handle(Request $request)
  {
    $sendData = $this->getSendData($request->input('order_number'));
    if(!$sendData->success) return $sendData;

    return Http::b1servicos()
      ->post('/message/tracking-update', $sendData->content)
      ->object();
  }

  private function getSendData(string $orderNumber): object
  {
    $order = Order::where('online_order_number', $orderNumber)->first();
    $address = DB::table('order_addresses')->where('online_order_number', $orderNumber)->first();

    if(!isset($order) || !isset($address)) return (object) [
      'success' => false,
      'reason' => $this->errorMessage($order, $address, $orderNumber),
    ];

    $shipment = $order->id_delivery_method === self::ENVIA_ID
      ? (new EnviaDotCom())->getShipment($order->tracking_code)['data'][0]
      : null;
  
    if($order->id_delivery_method === self::ENVIA_ID && !isset($shipment)) return (object) [
      'success' => false,
      'reason' => 'Não foi possível recuperar o rastreio do Envia.com',
    ];

    $deliveryMethod = DB::table('delivery_methods')->where('id', $order->id_delivery_method)->first();

    if(!isset($deliveryMethod)) return (object) [
      'success' => false,
      'reason' => 'Pedido não tem forma de envio definida',
    ];

    $updateStatusResponse = $this->getUpdateStatus($order->tracking_code, $deliveryMethod->name);

    if(!$updateStatusResponse->success) return $updateStatusResponse;

    $sellercentral = DB::table('sellercentrals')->where('id', $order->id_sellercentral)->first()->name;

    return (object) [
      'success' => true,
      'content' => [
        'order_number' => $orderNumber,
        'client_name' => $address->buyer_name,
        'client_email' => $address->buyer_email,
        'delivery_method' => $this->getDeliveryMethodName($deliveryMethod->name, $shipment),
        'tracking_code' => $order->tracking_code,
        'link' => $this->getLink(
          $deliveryMethod->id, 
          $order->tracking_code, 
          $address->postal_code, 
          $address->country,
          $shipment,
        ),
        'update_status' => $updateStatusResponse->content,
        'sellercentral' => $sellercentral,
        'company' => $this->getCompanyName($order->id_company),
      ]
    ];
  }

  private function getDeliveryMethodName(string $currentName, array | null $shipment): string
  {
    if($currentName === 'Envia.com') {
      return $this->mapEnviaNameToDeliveryMethod($shipment['name']);
    }

    return $currentName;
  }

  private function mapEnviaNameToDeliveryMethod(string $enviaName): string
  {
    if($enviaName === 'usps') return 'USPS';
    if($enviaName === 'correos') return 'Correos Espana';
    if($enviaName === 'envialia') return 'Envialia';

    return $enviaName;
  }

  private function getUpdateStatus(string $trackingCode, string $deliveryMethodName): object
  {
    [$response, $code] = (new UpdateOrInsertOrderTrackingAction())->handle($trackingCode, $deliveryMethodName);
    $success = $code === 200;

    if(!$success) return (object) [
      'success' => false,
      'reason' => $response,
    ];

    return (object) [
      'success' => true,
      'content' => $response['status'] . (isset($response['details']) ? " - " . $response['details'] : '')
    ];
  }

  private function getLink(
    int $deliveryMethodId, 
    string $trackingCode,
    string $postalCode,
    string $country,
    array | null $shipment,
  ): string | null
  {
    if($deliveryMethodId === 2) return "https://rastreamento.correios.com.br/app/index.php?objetos=$trackingCode";
    if($deliveryMethodId === 3) return "https://www.dhl.com/br-pt/home/tracking/tracking-express.html?submit=1&tracking-id=$trackingCode";
    if($deliveryMethodId === 4) return "https://www.fedex.com/fedextrack/?trknbr=$trackingCode";
    if($deliveryMethodId === 5) return "https://www.jadlog.com.br/tracking?cte=$trackingCode";
    if($deliveryMethodId === 8) return $this->getEnviaDeliveryMethod($shipment, $trackingCode, $postalCode, $country);
    if($deliveryMethodId === 11) return "https://www.kangu.com.br/rastreio";
    if($deliveryMethodId === 13) return "https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=$trackingCode";

    return null;
  }

  private function getEnviaDeliveryMethod(array $shipment, string $trackingCode, string $postalCode, string $country): string
  {
    ['name' => $name] = $shipment;

    if($name === 'usps') return "https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=$trackingCode";

    if($name === 'envialia') {
      $cp = substr($postalCode, 0, 4);
      return "https://www.envialia.com/acompanhamento/?t=t&v=$trackingCode&cp=$cp";
    }

    if($name === 'correos') {
      if(in_array($country, ['PT', 'PRT'])) return 'https://www.correosexpress.pt';
      if(in_array($country, ['ES', 'ESP'])) return 'https://www.correos.es';

      return 'https://www.correosexpress.pt, https://www.correos.es';
    }

    return "https://envia.com/pt-BR/monitorando";
  }

  private function errorMessage(?object $order, ?object $address, string $orderNumber)
  {
    if(!isset($order)) return "Pedido de número $orderNumber inexistente no sistema...";
    if(!isset($address)) return "Endereço do pedido $order->online_order_number inexistente no sistema...";
  }
}
