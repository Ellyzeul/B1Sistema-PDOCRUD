<?php namespace App\Http\Controllers;

use App\Models\SupplierURL;


class SupplierURLController
{
    public static function read(string $id)
    {
        $response = SupplierURL::read($id);

        return $response;
    }

    public static function update(
        int $id,
        string $url
    )
    {
        $response = SupplierURL::update($id, $url);

        return $response;
    }
}
