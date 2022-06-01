<?php namespace App\Models;

use \PDOCrud;


class SupplierURL
{
    public static function read(int $id)
    {
        $db = (new PDOCrud())->getPDOModelObj();

        $result = $db->executeQuery("
            SELECT url
            FROM supplier_url
            WHERE id_control = ?
        ", [$id]);

        return isset($result[0]['url']) ? $result[0] : [
            "url" => null
        ];
    }

    public static function update(
        int $id,
        string $url
    )
    {
        $db = (new PDOCrud())->getPDOModelObj();

        $db->insertOnDuplicateUpdate(
            "supplier_url",
            [
                "id_control" => $id, 
                "url" => $url
            ],
            [
                "url" => $url
            ]
        );

        return [
            "message" => "URL inserida"
        ];
    }
}
