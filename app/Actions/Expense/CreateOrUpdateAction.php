<?php

namespace App\Actions\Expense;

use App\Models\Expense;
use App\Models\ExpenseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateOrUpdateAction
{
  public function handle(Request $request)
  {
    $expense = $this->handleExpense($request);

    $this->handleExpenseDocuments($request->input('receipts'), $expense);
    $this->handleExpenseDocuments($request->input('documents'), $expense);
  }

  private function handleExpense(Request $request)
  {
    $data = $request->input('expense');
    
    if(isset($data['id'])) return $this->handleExpenseUpdate($data);

    return $this->handleExpenseInsert($data);
  }

  private function handleExpenseInsert(array $data)
  {
    $expense = new Expense($data);
    $expense->save();

    return $expense;
  }

  private function handleExpenseUpdate(array $data)
  {
    $expense = Expense::find($data['id']);

    $expense->update($data);

    return $expense;
  }

  private function handleExpenseDocuments(array $documents, Expense $expense)
  {
    collect($documents)->each(function(array $data) use($expense) {
      if(($data['delete'] ?? false) === true) {
        Expense::find($data['key'])->delete();
        return;
      }

      if(ExpenseDocument::where('key', $data['key'])->exists()) {
        $document = ExpenseDocument::find($data['key']);
      }
      else {
        $document = new ExpenseDocument();
      }

      $document->key = $data['key'];
      $document->created_at = $data['created_at'];
      $document->type = $data['type'];
      $document->issuer = $data['issuer'] ?? null;
      $document->value = $data['value'];
      $document->filename = $this->handleFile($data['file'], $data['extension']);
      $document->expense_id = $expense->id;

      $document->save();
    });
  }

  private function handleFile(string|null $file, string|null $ext)
  {
    if($file === null) return null;

    $filename = Str::uuid() . ".$ext";
    Storage::disk('documents')->put($filename, base64_decode($file));

    return $filename;
  }
}
