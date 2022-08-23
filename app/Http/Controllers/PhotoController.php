<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;

class PhotoController extends Controller
{
    public static function create(Request $request)
    {
        Log::info("/api/photo/create acessada");
        $photoFile = $request->file("photo");
        $photoName = $photoFile->getClientOriginalName();

        $photo = new Photo();
        $response = $photo->create($photoFile, $photoName);
        Log::info($response["message"]);

        return $response;
    }

    public static function read(Request $request)
    {
        $photoNamePattern = $request->input('name_pattern') ?? "";
        $photo = new Photo();
        $response = $photo->read($photoNamePattern);
        Log::info($response["message"]);

        return $response;
    }

    public static function verifyFromList(Request $request)
    {
        $numbersList = $request->input('numbers_list') ?? "";
        $photo = new Photo();
        $response = $photo->verifyFromList($numbersList);

        return $response;
    }
}
