<?php

namespace App\Actions\Expense;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Supplier;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ReadAction
{
  public function handle(Request $request)
  {
    $month = Date::createFromFormat(
      'Y-m-d',
      $request->input('month', Date::now()->format("Y-m")) . '-01'
    );

    return [
      'categories' => $this->parseArrayToMap(ExpenseCategory::get()),
      'bank' => DB::table('companies_accounts')->get(),
      'payment_methods' => $this->parseArrayToMap(PaymentMethod::get(), name: 'operation'),
      'suppliers' => Supplier::get(),
      'expenses' => Expense::whereBetween(
        'due_date',
        [$month->format('Y-m-01'), $month->format('Y-m-t')]
      )->get(),
    ];
  }

  private function parseArrayToMap(Collection $collection, string $id = 'id', string $name = 'name')
  {
    $map = [];
    $registries = $collection
      ->map(fn($registry) => ['id' => $registry->{$id}, 'name' => $registry->{$name}])
      ->unique('id');

    foreach($registries as $registry) {
      $map[$registry['id']] = $registry['name'];
    }

    return $map;
  }
}
