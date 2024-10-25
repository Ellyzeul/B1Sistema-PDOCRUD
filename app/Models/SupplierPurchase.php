<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SupplierPurchase extends Model
{
    use HasFactory;

    protected $table = 'supplier_purchase';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['id_supplier', 'purchase_method', 'id_payment_method', 'id_company', 'freight', 'sales_total', 'observation', 'payment_date'];
    protected $casts = ['freight' => 'float', 'sales_total' => 'float'];
    protected $appends = ['supplier', 'items'];

    public function getSupplierAttribute()
    {
        return Supplier::find($this->id_supplier)->name;
    }

    public function getItemsAttribute(): Collection
    {
        return SupplierPurchaseItems::where('id_purchase', $this->id)->get([
            'id',
            'id_order',
            'id_purchase',
            'value',
            'status',
            'invoice_key',
        ]);
    }
}
