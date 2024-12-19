<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['annotations', 'supplier', 'bank_id', 'company_id', 'due_date', 'expense_category_id', 'id', 'on_financial', 'payment_date', 'payment_method_id', 'status', 'value'];
}
