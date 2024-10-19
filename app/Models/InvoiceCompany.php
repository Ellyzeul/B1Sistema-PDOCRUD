<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCompany extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'cnpj';
    protected $keyType = 'string';

    protected $fillable = ['cpnj', 'name', 'ie', 'uf'];
}
