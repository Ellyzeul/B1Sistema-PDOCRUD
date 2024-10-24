<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Request;

class ReadAction
{
  public function handle(Request $_)
  {
    $invoices = Invoice::where('emitted_at', '>=', now()->subMonths(2))->get();
    return [
      'linked' => $invoices->filter(fn(Invoice $item) => $item->match === 'linked'),
      'partially_linked' => $invoices->filter(fn(Invoice $item) => $item->match === 'partially_linked'),
      'not_linked' => $invoices->filter(fn(Invoice $item) => $item->match === 'not_linked'),
    ];
  }
}
