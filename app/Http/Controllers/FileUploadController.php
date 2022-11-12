<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FileUpload;

class FileUploadController extends Controller
{
    public function orderUpdate(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input("upload_data");
        $response = $fileUpload->orderUpdate($data);

        return $response;
    }

    public function orderInsert(Request $request)
    {
        $fileUpload = new FileUpload();
        $data = $request->input("upload_data");
        $response = $fileUpload->orderInsert($data);

        return $response;
    }
}
