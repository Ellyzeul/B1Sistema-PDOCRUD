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

    protected $fillable = ['id_purchase', 'id_order', 'value'];

    public function purchase()
    {
        return $this->belongsTo(SupplierPurchase::class);
    }
}
