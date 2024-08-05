<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $primaryKey = 'id';

    protected $fillable = ['name'];

    public $timestamps = false;

    public static function idFromName(string $name)
    {
        $supplier = Supplier::where('name', $name)->first();

        if(!isset($supplier)) {
            $supplier = new Supplier();
            $supplier->name = $name;
            $supplier->save();
        }

        return $supplier->id;
    }
}
