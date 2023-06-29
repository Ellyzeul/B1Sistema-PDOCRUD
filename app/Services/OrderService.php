<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\Order\ImportOrdersFromDateAction;

class OrderService
{
  public function importOrdersFromDate(Request $request)
  {
    return (new ImportOrdersFromDateAction())->handle($request);
  }
}
