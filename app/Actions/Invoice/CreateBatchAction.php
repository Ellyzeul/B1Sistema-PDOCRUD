<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceCompany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CreateBatchAction
{
  public function handle(Collection $invoices)
  {
    return [
      'success' => $invoices
        ->map(fn(array $data) => $this->createInvoice($data))
        ->reduce(fn($acc, $cur) => $acc && $cur, true)
    ];
  }

  private function createInvoice(array $data)
  {
    foreach(['recipient', 'emitter', 'courier'] as $company) {
      $cnpj = $this->createInvoiceCompanyIfNotExists($data[$company]);
      unset($data[$company]);
      $data["{$company}_cnpj"] = $cnpj;
    }
    return (new Invoice($data))->save();
    try {
      return (new Invoice($data))->save();
    }
    catch(QueryException $err) {
      return $err->errorInfo[1] === '1062';
    }
  }

  private function createInvoiceCompanyIfNotExists(array $data)
  {
    if($data['cnpj'] === null || InvoiceCompany::where('cnpj', $data['cnpj'])->exists()) {
      return $data['cnpj'];
    }

    $company = new InvoiceCompany($data);
    $company->cnpj = $data['cnpj'];
    $company->save();

    return $company->cnpj;
  }
}
