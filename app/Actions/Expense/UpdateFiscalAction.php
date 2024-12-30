<?php

namespace App\Actions\Expense;

use App\Models\Expense;
use Illuminate\Http\Request;

class UpdateFiscalAction
{
  public function handle(Request $request)
  {
    $expense = Expense::find($request->id);

    $expense->fiscal = $request->fiscal;
    $expense->save();

    return;
  }
}
