<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Quote extends Model
{
    use HasFactory;

    public function read()
    {
        $result = DB::select("CALL select_random_quote()")[0];

        return $result;
    }
}
