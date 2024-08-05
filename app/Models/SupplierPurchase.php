<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class SupplierPurchase extends Model
{
    use HasFactory;

    protected $table = 'supplier_purchase';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['id_supplier', 'purchase_method', 'id_company', 'freight', 'sales_total'];
    protected $casts = ['freight' => 'float', 'sales_total' => 'float'];
    protected $appends = ['supplier', 'items'];

    public function items()
    {
        return $this->hasMany(SupplierPurchaseItems::class);
    }

    public function getSupplierAttribute()
    {
        return Supplier::find($this->id_supplier)->name;
    }

    public function getItemsAttribute()
    {
        return SupplierPurchaseItems::where('id_purchase', $this->id)->get([
            'id',
            'id_order',
            'value',
        ]);
    }
}
