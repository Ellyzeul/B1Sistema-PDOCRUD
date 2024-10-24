<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseItems extends Model
{
    use HasFactory;

    protected $table = 'supplier_purchase_items';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['id_purchase', 'id_order', 'value', 'status', 'invoice_key'];

    protected $casts = [
        'value' => 'float',
    ];

    protected $appends = ['supplier', 'items_on_purchase'];

    public function getSupplierAttribute()
    {
        $purchase = SupplierPurchase::find($this->id_purchase);
        if($purchase === null) return null;

        return Supplier::find($purchase->id_supplier);
    }

    public function getItemsOnPurchaseAttribute()
    {
        return self::where('id_purchase', $this->id_purchase)->get()->count();
    }
}
