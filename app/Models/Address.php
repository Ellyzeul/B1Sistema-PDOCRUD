<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'order_addresses';
    protected $primaryKey = 'online_order_number';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['online_order_number', 'recipient_name', 'address_1', 'address_2', 'address_3', 'county', 'city', 'state', 'postal_code', 'country', 'buyer_phone', 'buyer_name', 'buyer_email', 'expected_date', 'price', 'freight', 'item_tax', 'freight_tax', 'ship_phone', 'delivery_instructions', 'cpf_cnpj'];
}
