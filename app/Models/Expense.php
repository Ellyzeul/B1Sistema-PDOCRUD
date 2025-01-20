<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['annotations', 'supplier', 'bank_id', 'company_id', 'due_date', 'expense_category_id', 'id', 'on_financial', 'payment_date', 'payment_method_id', 'status', 'value', 'type'];

    protected $appends = ['invoices', 'receipts', 'documents'];

    protected $casts = [
        'value' => 'float',
    ];

    public function getInvoicesAttribute()
    {
        return Invoice::whereIn(
            'key',
            SupplierPurchaseItems::where('id_purchase', $this->supplier_purchase_id)
                ->get()
                ->map(fn(SupplierPurchaseItems $item) => $item->invoice_key)
                ->values()
        )->get();
    }

    public function getReceiptsAttribute()
    {
        return ExpenseDocument::where('expense_id', $this->id)
            ->where('type', 'recibo')
            ->get();
    }

    public function getDocumentsAttribute()
    {
        return ExpenseDocument::where('expense_id', $this->id)
            ->where('type', '<>', 'recibo')
            ->get();
    }
}
