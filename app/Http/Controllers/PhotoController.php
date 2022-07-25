<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;

class PhotoController extends Controller
{
    public static function create(UploadedFile $photoFile, string $photoName)
    {
        $photo = new Photo();
        $response = $photo->create($photoFile, $photoName);
        Log::info($response["message"]);

        return $response;
    }

    public static function read(string $photoNamePattern)
    {
        $photo = new Photo();
        $response = $photo->read($photoNamePattern);
        Log::info($response["message"]);

        return $response;
    }
}
