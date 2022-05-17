<?php namespace B1system\Model;

use \PDOCrud;


class Order
{
    public static function read(PDOCrud $crud)
    {
        $crud = Order::setFields($crud);
        $crud = Order::fieldsNotMandatory($crud);
        $crud = Order::fieldBulkUpdate($crud);

        $response = $crud->dbTable("order_control");

        return $response;
    }

    private static function setFields(PDOCrud $crud)
    {
        $crud->crudTableCol([
            "online_order_number",
            "id_sellercentral",
            "invoice_number",
            "bling_number",
            "order_date",
            "expected_date",
            "selling_price",
            "ask_rating"
        ]);

        $crud->colRename("online_order_number", "ORIGEM");
        $crud->colRename("id_sellercentral", "Exportação");
        $crud->colRename("invoice_number", "NF");
        $crud->colRename("bling_number", "Nº Bling");
        $crud->colRename("order_date", "Data do pedido");
        $crud->colRename("expected_date", "Data para entrega");
        $crud->colRename("selling_price", "Valor");
        $crud->colRename("ask_rating", "Pedir avaliação");
        
        return $crud;
    }

    private static function fieldsNotMandatory(PDOCrud $crud)
    {
        $crud->fieldNotMandatory("invoice_number");
        $crud->fieldNotMandatory("bling_number");
        $crud->fieldNotMandatory("ask_rating");

        return $crud;
    }

    private static function fieldBulkUpdate(PDOCrud $crud)
    {
        $crud->bulkCrudUpdate("invoice_number", "text");
        $crud->bulkCrudUpdate("bling_number", "text");
        $crud->bulkCrudUpdate("ask_rating", "select", ['askrating_key', 'askrating_val'], [
            [1, "Sim"],
            [0, "Não"]
        ]);

        return $crud;
    }

    private static function fieldsType(PDOCrud $crud)
    {
        $crud->fieldTypes("id_sellercentral", "select");
        $query = "SELECT id, name FROM sellercentrals";
        $crud->fieldDataBinding("id_sellercentral", $query, "id", ["id", "name"],"sql", " - ");

        $crud->fieldTypes("order_date", "date");
        $crud->fieldTypes("ask_rating", "int");

        return $crud;
    }
}
