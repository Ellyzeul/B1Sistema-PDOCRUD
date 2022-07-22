<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use \PDOCrud;

class Photo extends Model
{
    use HasFactory;

    public static function create(UploadedFile $photo, string $photoName) {
        $sep = DIRECTORY_SEPARATOR;
        $photoSavePath = $_SERVER['DOCUMENT_ROOT'] . $sep . "photos";
        Log::info("Tentando salvar a foto " . $photoName);

        $photo->move(
            $photoSavePath, 
            $photoName
        );

        return [
            "message" => "Foto salva com sucesso!"
        ];
    }
}
