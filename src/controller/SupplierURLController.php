<?php namespace B1system\Controller;

use B1system\Model\SupplierURL;
use B1system\View\APIView;


class SupplierURLController
{
    public static function read(string $id)
    {
        $processed = SupplierURL::read($id);
        $response = APIView::render($processed);

        return $response;
    }

    public static function update(
        int $id,
        string $url
    )
    {
        $processed = SupplierURL::update($id, $url);
        $response = APIView::render($processed);

        return $response;
    }
}
