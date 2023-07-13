<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\Order\ImportOrdersFromDateAction;
use App\Actions\Order\AcceptFNACOrderAction;
use App\Actions\Order\UpdateAddressVerifiedActionAction;

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
}
