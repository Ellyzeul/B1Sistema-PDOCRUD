<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;

class PhotoController extends Controller
{
    public static function create(UploadedFile $photo, string $photoName) {
        $response = Photo::create($photo, $photoName);
        Log::info($response["message"]);

        return $response;
    }
}
