<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use \PDOCrud;

class Photo extends Model
{
    use HasFactory;

    private FilesystemAdapter $disk;
    private string $savePath;
    private string $readPath;

    public function __construct()
    {
        $this->disk = Storage::disk('orders-photos');
        $this->savePath = storage_path('app/public/photos');
        $this->readPath = 
            (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://" . (
                $_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1"
                ? $_SERVER['SERVER_NAME'] . ":" . $_SERVER["SERVER_PORT"]
                : $_SERVER['SERVER_NAME']
            ) . "/storage/photos/";
    }

    public function create(UploadedFile $photoFile, string $photoName)
    {
        [$number, $extension] = explode(".", $photoName);

        $copies = array_filter(
            $this->disk->files(), 
            function($item) use ($number) {
                return str_starts_with($item, $number);
            }
        );
        $copyNumber = count($copies);

        $saveName = $copyNumber == 0
            ? "$number.$extension"
            : $number . "_" . $copyNumber . "." . $extension;

        $photoFile->move(
            $this->savePath, 
            $saveName
        );

        return [
            "message" => "Foto $photoName salva com sucesso!"
        ];
    }

    public function read(string $photoNamePattern)
    {
        $photos = $this->disk->files();

        $links = array_map(function($item) {
            return $this->readPath . $item;
        }, array_filter($photos, function($item) use ($photoNamePattern) {
            return str_starts_with($item, $photoNamePattern);
        }));

        return [
            "message" => "Fotos com o padrão buscado foram retornadas!",
            "photos" => array_values($links)
        ];
    }

    public function verifyFromList(array $numbers)
    {
        $response = [];
        $photos = $this->disk->files();
        foreach($numbers as $number) {
            $response[$number] = $this->array_some($photos, function($photo) use ($number) {
                return str_starts_with($photo, $number);
            });
        }

        return $response;
    }

    public function exclude(string $photoName)
    {

        $this->disk->delete($photoName);

        return [
            "message" => "A imagem $photoName foi deletada!"
        ];
    }

    private function array_some(array $array, callable $fn)
    {
        foreach($array as $value) {
            if($fn($value)) return true;
        }

        return false;
    }
}