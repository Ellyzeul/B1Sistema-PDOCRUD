<?php namespace B1system\Model;

use \PDOCrud;


class Order
{
    public static function read(PDOCrud $crud)
    {
        $crud = Order::setFields($crud);
        $crud = Order::fieldsNotMandatory($crud);
        $crud = Order::fieldBulkUpdate($crud);
        $crud = Order::fieldsFormatting($crud);
        $crud = Order::crudSettings($crud);

        $crud->dbOrderBy("id desc");

        $response = $crud->dbTable("order_control");

        return $response;
    }

    private static function setFields(PDOCrud $crud)
    {
        $crud->crudTableCol([
            "id_sellercentral",
            "id_phase",
            "invoice_number",
            "online_order_number",
            "bling_number",
            "order_date",
            "expected_date",
            "supplier_name",
            "purchase_date",
            "id_delivery_address",
            "supplier_purchase_number",
            "id_delivery_method",
            "tracking_code",
            "collection_code",
            "delivered_date",
            "ask_rating"
        ]);

        $crud->colRename("id_sellercentral", "Exportação");
        $crud->colRename("id_phase", "Fase do processo");
        $crud->colRename("invoice_number", "NF");
        $crud->colRename("online_order_number", "ORIGEM");
        $crud->colRename("bling_number", "Nº Bling");
        $crud->colRename("order_date", "Data do pedido");
        $crud->colRename("expected_date", "Data prevista");
        $crud->colRename("selling_price", "Valor");
        $crud->colRename("supplier_name", "Fornecedor");
        $crud->colRename("purchase_date", "Data da compra");
        $crud->colRename("id_delivery_address", "Endereço de entrega");
        $crud->colRename("supplier_purchase_number", "Nº Compra fornecedor");
        $crud->colRename("id_delivery_method", "Forma de envio");
        $crud->colRename("tracking_code", "Código de rastreio");
        $crud->colRename("collection_code", "Código de coleta");
        $crud->colRename("delivered_date", "Data de entrega");
        $crud->colRename("ask_rating", "Pedir avaliação");
        
        return $crud;
    }

    private static function fieldsNotMandatory(PDOCrud $crud)
    {
        $crud->fieldNotMandatory("invoice_number");
        $crud->fieldNotMandatory("bling_number");
        $crud->fieldNotMandatory("supplier_name");
        $crud->fieldNotMandatory("purchase_date");
        $crud->fieldNotMandatory("id_delivery_address");
        $crud->fieldNotMandatory("supplier_purchase_number");
        $crud->fieldNotMandatory("id_delivery_method");
        $crud->fieldNotMandatory("tracking_code");
        $crud->fieldNotMandatory("collection_code");
        $crud->fieldNotMandatory("delivered_date");
        $crud->fieldNotMandatory("ask_rating");

        return $crud;
    }

    private static function getBulkUpdateData(PDOCrud $crud, string $table, \Closure $nameMaker, string $key='id', string $val='name')
    {
        $db = $crud->getPDOModelObj();
        $results = $db->select($table);

        $data = [];
        foreach($results as $pair) {
            array_push($data, [
                $pair[$key],
                $nameMaker($pair, $key, $val)
            ]);
        }

        return $data;
    }

    private static function fieldBulkUpdate(PDOCrud $crud)
    {
        $phases = Order::getBulkUpdateData($crud, 'phases', function($pair, $key, $val)
        {
            return (fmod($pair[$key], 1) == 0 ? $pair[$key] . ".0" : $pair[$key]) . " - " . $pair[$val];
        });

        $deliveryAddresses = Order::getBulkUpdateData($crud, 'delivery_addresses', function($pair, $key, $val)
        {
            return $pair[$val];
        });

        $deliveryMethods = Order::getBulkUpdateData($crud, 'delivery_methods', function($pair, $key, $val)
        {
            return $pair[$val];
        });

        $crud->bulkCrudUpdate("id_phase", "select", ['phase_key' => 'phase_val'], $phases);
        $crud->bulkCrudUpdate("invoice_number", "text");
        $crud->bulkCrudUpdate("bling_number", "text");
        $crud->bulkCrudUpdate("supplier_name", "text");
        $crud->bulkCrudUpdate("purchase_date", "date");
        $crud->bulkCrudUpdate("id_delivery_address", "select", ['deliveryaddress_key' => 'deliveryaddress_val'], $deliveryAddresses);
        $crud->bulkCrudUpdate("supplier_purchase_number", "text");
        $crud->bulkCrudUpdate("id_delivery_method", "select", ['deliverymethod_key' => 'deliverymethod_val'], $deliveryMethods);
        $crud->bulkCrudUpdate("tracking_code", "text");
        $crud->bulkCrudUpdate("collection_code", "text");
        $crud->bulkCrudUpdate("delivered_date", "date");
        $crud->bulkCrudUpdate("ask_rating", "select", ['askrating_key' => 'askrating_val'], [
            [1, "Sim"],
            [0, "Não"]
        ]);

        return $crud;
    }

    private static function fieldsFormatting(PDOCrud $crud)
    {
        $db = $crud->getPDOModelObj();
        $sellercentrals = $db->select("sellercentrals");
        $phases = $db->select('phases');

        foreach($sellercentrals as $sel) {
            $crud->tableColFormatting("id_sellercentral", "replace", [$sel["id"] => $sel["name"]]);
        }
        $crud->tableColFormatting("order_date", "date", ["format" => "d/m/Y"]);
        $crud->tableColFormatting("expected_date", "date", ["format" => "d/m/Y"]);
        $crud->tableColFormatting("purchase_date", "date", ["format" => "d/m/Y"]);
        $crud->tableColFormatting("delivered_date", "date", ["format" => "d/m/Y"]);

        return $crud;
    }

    private static function crudSettings(PDOCrud $crud)
    {
        $crud->setSettings("actionbtn", false);
        $crud->setSettings("checkboxCol", false);

        return $crud;
    }
}
