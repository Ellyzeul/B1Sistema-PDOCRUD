<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\Order\ImportOrdersFromDateAction;
use App\Actions\Order\AcceptFNACOrderAction;
use App\Actions\Order\UpdateAddressVerifiedActionAction;
use App\Actions\Order\ReadOrderControlByOrderNumberAction;
use App\Actions\Order\ReadOrderAddressesByOrderNumberAction;
use App\Actions\Order\SendOrderToBlingAction;

class OrderService
{
  public function importOrdersFromDate(Request $request)
  {
    return (new ImportOrdersFromDateAction())->handle($request);
  }

  public function acceptFNACOrder(Request $request)
  {
    return (new AcceptFNACOrderAction())->handle($request);
  }

  public function updateAddressVerified(Request $request)
  {
    return (new UpdateAddressVerifiedActionAction())->handle($request);
  }

  public function readOrderControlByOrderNumber(Request $request) {
    return (new ReadOrderControlByOrderNumberAction())->handle($request);
  }

  public function ReadOrderAddressesByOrderNumber(Request $request) {
    return (new ReadOrderAddressesByOrderNumberAction())->handle($request);
  }

  public function sendOrderToBling(Request $request)
  {
    $order = $request->input('order');
    $client = $request->input('client');
    $items = $request->input('items');
    $idCompany = $request->input('id_company');

    return (new SendOrderToBlingAction())->handle($order, $client, $items, $idCompany);
  }
}
