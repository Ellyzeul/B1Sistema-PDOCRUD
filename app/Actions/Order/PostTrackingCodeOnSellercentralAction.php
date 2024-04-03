<?php namespace App\Actions\Order;

use App\Models\Order;
use App\Services\ThirdParty\B1Servicos;
use App\Services\ThirdParty\FNAC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostTrackingCodeOnSellercentralAction
{
  private array $handlers;

  public function __construct()
  {
    $this->handlers = [
      'Amazon' => function(Request $request) {
        $orderNumber = $request->input('orderNumber');

        return (new B1Servicos())->orderTrackingCodePost(
          $orderNumber,
          $request->input('sellercentral'),
          $request->input('company'),
          $request->input('trackingNumber'),
          $request->input('shipDate'),
          $request->input('service') ?? $this->getOrderDeliveryMethod($orderNumber),
        );
      },
      'FNAC' => function(Request $request) {
        $idCompany = $request->input('company') === 'seline' ? 0 : 1;
        $country = Str::of($request->input('sellercentral'))->pipe(function($str) {
          $str = Str::after($str, '-');
          return Str::lower($str);
        });
        $orderNumber = $request->input('orderNumber');

        return (new FNAC($country, $idCompany))->ordersUpdate(
          $orderNumber,
          'update_all',
          [
            'tracking_number' => $request->input('trackingNumber'),
            'tracking_company' => $request->input('service') ?? $this->getOrderDeliveryMethod($orderNumber),
          ],
        );
      }
    ];
  }

  public function handle(Request $request)
  {
    $sellercentralPrefix = Str::before($request->input('sellercentral'), '-');
    Log::debug($sellercentralPrefix);

    if(!isset($this->handlers[$sellercentralPrefix])) return [
      'success' => false,
      'reason' => "Canal de venda $sellercentralPrefix não está configurado",
    ];

    return $this->handlers[$sellercentralPrefix]($request);
  }

  private function getOrderDeliveryMethod(string $orderNumber)
  {
    $deliveryMethodId = Order::select('id_delivery_method')
      ->where('online_order_number', $orderNumber)
      ->first()
      ->id_delivery_method;

    return DB::table('delivery_methods')
      ->select('name')
      ->where('id', $deliveryMethodId)
      ->first()
      ->name;
  }
}
