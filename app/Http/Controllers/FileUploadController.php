<?php

namespace App\Http\Controllers;

use App\Actions\Invoice\CreateBatchAction;
use Illuminate\Http\Request;

use App\Models\FileUpload;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
    public function orderUpdate(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input('upload_data');
        $response = $fileUpload->orderUpdate($data);

        return $response;
    }

    public function orderAmazonInsert(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input('upload_data');
        $response = $fileUpload->orderAmazonInsert($data);

        return $response;
    }

    public function orderNuvemshopInsert(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input('upload_data');
        $response = $fileUpload->orderNuvemshopInsert($data);

        return $response;
    }

    public function orderEstanteInsert(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input('upload_data');
        $response = $fileUpload->orderEstanteInsert($data);

        return $response;
    }
    public function orderAlibrisInsert(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input('upload_data');
        $response = $fileUpload->orderAlibrisInsert($data);

        return $response;
    }

    public function orderFNACInsert(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input('upload_data');
        $response = $fileUpload->orderFNACInsert($data);

        return $response;
    }

    public function fsistIngestion(Request $request)
    {
        (new CreateBatchAction())->handle(collect($request->input('upload_data'))
            ->map(function(array $item) {
                foreach(['emitter', 'recipient', 'courier'] as $subject) {
                    $item[$subject] = [
                        'cnpj' => $item["{$subject}_cnpj"],
                        'name' => $item["{$subject}_name"],
                        'ie' => $item["{$subject}_ie"],
                        'uf' => $item["{$subject}_uf"],
                    ];
                    $item['emitted_at'] = Date::createFromTimestampUTC($item['emitted_at'])->format('Y-m-d H:i:s');
                    $item['status'] = $item['status'] === 'Autorizada' ? 'authorized' : 'cancelled';
                    $item['type'] = $item['type'] === 'Saída' ? 'out' : 'in';
                    $item['manifestation'] = $item['manifestation'] === 'Confirmada' ? 'confirmed' : 'acknowledged';
                    $item['has_xml'] = $item['has_xml'] === 'Sim';
                    
                    unset($item["{$subject}_cnpj"]);
                    unset($item["{$subject}_name"]);
                    unset($item["{$subject}_ie"]);
                    unset($item["{$subject}_uf"]);
                }

                return $item;
            })
        );
    }
}
