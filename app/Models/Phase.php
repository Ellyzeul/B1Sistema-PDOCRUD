<?php namespace App\Models;

use Illuminate\Support\Facades\DB;

class Phase
{
    public static function read()
    {
        $phases = DB::table('phases')->get();
        $response = [
            "phases" => $phases
        ];

        return $response;
    }
}
