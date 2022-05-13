<?php namespace B1system\Model;

use \PDOCrud;


class Order
{
    public static function read(PDOCrud $crud) {
        $crud->crudTableCol([
            "online_order_number",
            "id_sellercentral",
            "invoice_number",
            "bling_number",
            "order_date",
            "expected_date",
            "selling_price",
            "id_ask_rating"
        ]);

        $response = $crud->dbTable("orders");

        return $response;
    }
}
