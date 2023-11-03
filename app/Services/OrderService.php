<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\Order\ImportOrdersFromDateAction;
use App\Actions\Order\AcceptFNACOrderAction;
use App\Actions\Order\GetOrderMessagesAction;
use App\Actions\Order\PostOrderAddressOnEnviaDotComAction;
use App\Actions\Order\PostOrderMessageAction;
use App\Actions\Order\UpdateAddressVerifiedAction;
use App\Actions\Order\ReadOrderControlByOrderNumberAction;
use App\Actions\Order\ReadOrderAddressesByOrderNumberAction;
use App\Actions\Order\SendOrderToBlingAction;
use App\Actions\Order\UpdateCancelInvoiceAction;

class OrderService
{
  public function importOrdersFromDate(Request $request)
  {
    \ini_set('max_execution_time', 600);
    $response = (new ImportOrdersFromDateAction())->handle($request);
    \ini_set('max_execution_time', 60);
    
    return $response;
  }

  public function acceptFNACOrder(Request $request)
  {
    return (new AcceptFNACOrderAction())->handle($request);
  }

  public function updateAddressVerified(Request $request)
  {
    return (new UpdateAddressVerifiedAction())->handle($request);
  }

  public function updateCancelInvoice(Request $request)
  {
    return (new UpdateCancelInvoiceAction())->handle($request);
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

  public function getOrderMessages()
  {
    \ini_set('max_execution_time', 600);
    return (new GetOrderMessagesAction())->handle();
    \ini_set('max_execution_time', 60);
  }

  public function postOrderMessage(Request $request)
  {
    return (new PostOrderMessageAction())->handle($request);
  }

  public function postOrderAddressOnEnviaDotCom(Request $request)
  {
    return (new PostOrderAddressOnEnviaDotComAction())->handle($request);
  }
}
