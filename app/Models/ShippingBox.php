<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingBox extends Model
{
    use HasFactory;

    protected $table = 'shipping_box';
    protected $primaryKey = 'id';
}
