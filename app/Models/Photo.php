<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use \PDOCrud;

class Photo extends Model
{
    use HasFactory;

    private string $savePath;
    private string $readPath;

    public function __construct()
    {
        $sep = DIRECTORY_SEPARATOR;
        $this->savePath = $_SERVER['DOCUMENT_ROOT'] . $sep . "static" . $sep . "photos";
        $this->readPath = 
            (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://" . (
                $_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1"
                ? $_SERVER['SERVER_NAME'] . ":" . $_SERVER["SERVER_PORT"]
                : $_SERVER['SERVER_NAME']
            ) . "/static/photos/";
    }

    public function create(UploadedFile $photoFile, string $photoName)
    {
        Log::info("Tentando salvar a foto " . $photoName);

        DB::table("photos")->insert([
            "name" => $photoName
        ]);
        $result = DB::select("CALL select_most_recent_photo_number(?)", [
            $photoName
        ])[0];

        $nameParts = explode(".", $photoName);
        $photoDesc = $nameParts[0];
        $photoExtension = $nameParts[1];
        $photoNumber = $result->number;
        $photoNameWoutExt = $photoDesc.($photoNumber == 0 ? '' : "_$photoNumber");
        $saveName = $photoNameWoutExt.".$photoExtension";

        $photoFile->move(
            $this->savePath, 
            $saveName
        );

        return [
            "message" => "Foto $photoNameWoutExt salva com sucesso!"
        ];
    }

    public function read(string $photoNamePattern)
    {
        $results = DB::select("CALL select_photos_using_pattern(?)", [
            $photoNamePattern
        ]);
        $response = [
            "message" => "Fotos com o padrão buscado foram retornadas!",
            "photos" => []
        ];

        foreach($results as $result) {
            array_push(
                $response["photos"], 
                $this->readPath . $result->name
            );
        }

        return $response;
    }

    public function verifyFromList(string $numberList) {
        $results = DB::select(
            "CALL select_photos_from_list(?)",
            [$numberList]
        );
        if(isset($results[0]->critical_warning)) return ["message" => $results[0]->critical_warning];

        $photoNumbers = explode(",", $numberList);
        $response = [];
        foreach($photoNumbers as $number) $response[$number] = false;
        foreach($results as $result) $response[$result->name] = true;

        return $response;
    }
}
