<?php namespace B1system\Model;

use \PDOCrud;


class Order
{
    public static function read(PDOCrud $crud) {
        // $crud->joinTable("");

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

        $crud->fieldTypes("id_sellercentral", "select");
        $query = "SELECT id, name FROM sellercentrals";
        $crud->fieldDataBinding("id_sellercentral", $query, "id", ["id", "name"],"sql", " - ");

        $crud->fieldTypes("invoice_number", "null");
        $crud->fieldTypes("bling_number", "null");

        $crud->fieldTypes("order_date", "date");

        $response = $crud->dbTable("orders");

        return $response;
    }
}
