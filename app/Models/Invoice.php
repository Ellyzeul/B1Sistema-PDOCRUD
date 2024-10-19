<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['key', 'emitted_at', 'period', 'type', 'value', 'status', 'manifestation', 'emitter_cnpj', 'recipient_cnpj', 'courier_cnpj', 'has_xml', 'origin', 'cfops'];
}
