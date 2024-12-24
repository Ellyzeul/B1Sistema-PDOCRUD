<?php

namespace App\Actions\Expense;

use App\Models\Expense;
use Illuminate\Http\Request;

class CreateOrUpdateAction
{
  public function handle(Request $request)
  {
    $data = $request->all();
    
    if(isset($data['id'])) return $this->handleUpdate($data);

    return $this->handleInsert($data);
  }

  private function handleInsert(array $data)
  {
    $expense = new Expense($data);
    $expense->save();

    return $expense;
  }

  private function handleUpdate(array $data)
  {
    $expense = Expense::find($data['id']);

    $expense->update($data);

    return;
  }
}
