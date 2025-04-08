<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmittedInvoice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['key', 'number', 'emitted_at', 'order_number', 'company', 'link_danfe', 'link_xml', 'cancelled', 'cancelment_same_day'];
}
