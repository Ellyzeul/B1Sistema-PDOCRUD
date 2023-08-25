<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use App\Services\ThirdParty\FNAC;

class AcceptFNACOrderAction
{
  public function handle(Request $request)
  {
    $orderNumber = $request->input('order_number');
    $fnac = new FNAC('pt', 0);

    $response = $fnac->acceptOrder($orderNumber);

    return $response;
  }
}
