<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory;

    public function readThumbnails()
    {
        $results = DB::select("SELECT * FROM companies");

        $response = [
            "message" => "Thumbnails recuperadas",
            "thumbs" => []
        ];
        foreach($results as $company) {
            array_push($response["thumbs"], [
                "id" => $company->id,
                "thumbnail" => $company->thumbnail
            ]);
        }

        return $response;
    }
}
