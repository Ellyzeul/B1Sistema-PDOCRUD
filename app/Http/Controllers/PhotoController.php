<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;

class PhotoController extends Controller
{
    public static function create(Request $request)
    {
        Log::notice("Foto de pedido enviada para salvar");

        $photoFile = $request->file("photo");
        $photoName = $photoFile->getClientOriginalName();

        $photo = new Photo();
        $response = $photo->create($photoFile, $photoName);

        Log::info($response["message"]);

        return $response;
    }

    public static function read(Request $request)
    {
        Log::notice("Leitura de fotos de pedido");

        $photoNamePattern = $request->input('name_pattern') ?? "";

        $photo = new Photo();
        $response = $photo->read($photoNamePattern);

        Log::info($response["message"]);

        return $response;
    }

    public static function verifyFromList(Request $request)
    {
        $rawNumbers = $request->input('numbers_list') ?? "";
        $numbers = array_filter(explode(",", $rawNumbers), function($elem) {
            return $elem != "";
        });

        $photo = new Photo();
        $response = $photo->verifyFromList($numbers);

        return $response;
    }
}