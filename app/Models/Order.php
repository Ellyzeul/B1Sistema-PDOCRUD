<?php namespace App\Models;

use \PDOCrud;


class Order
{
    private static array $columnsPerPhase = [
        "id" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "id_sellercentral" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "id_phase" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "invoice_number" => ["2.4", "2.5", "2.6", "2.7", "2.8"],
        "online_order_number" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "bling_number" => ["2.4", "2.5", "2.6", "2.7", "2.8"],
        "order_date" => ["2.1", "2.3", "3.1", "3.2"],
        "expected_date" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "isbn" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "3.1", "3.2"],
        "selling_price" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.1", "2.3", "3.1", "3.2"],
        "supplier_name" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "3.1", "3.2"],
        "purchase_date" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3"],
        "id_delivery_address" => ["2.3", "3.1", "3.2"],
        "supplier_purchase_number" => ["2.1", "2.3"],
        "id_delivery_method" => ["2.4", "2.5", "2.6", "2.7", "2.8", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "tracking_code" => ["2.4", "2.5", "2.6", "2.7", "2.8", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "collection_code" => ["2.4", "2.5", "2.6", "2.7", "2.8"],
        "delivered_date" => ["2.4", "2.5", "2.6", "2.7", "2.8", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "ask_rating" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
    ];

    public static function read(string|null $phase)
    {
        $crud = new PDOCrud();
        [$crud, $columns] = Order::setFields($crud, $phase);
        $crud = Order::fieldsNotMandatory($crud, $columns);
        $crud = Order::fieldBulkUpdate($crud, $columns);
        $crud = Order::fieldsFormatting($crud, $columns);
        $crud = Order::fieldsFiltering($crud, $phase);
        $crud = Order::crudSettings($crud);

        $crud->dbOrderBy("id desc");

        $response = $crud->dbTable("order_control")->render();

        return $response;
    }

    private static function setFields(PDOCrud $crud, string|null $phase)
    {
        $columns = [];
        foreach(Order::$columnsPerPhase as $column => $validPhases) {
            if($phase != null ? \in_array($phase, $validPhases) : true) {
                array_push($columns, $column);
            }
        }
        $crud->crudTableCol($columns);

        in_array("id", $columns) ? $crud->colRename("id", "Nº") : null;
        in_array("id_sellercentral", $columns) ? $crud->colRename("id_sellercentral", "Exportação") : null;
        in_array("id_phase", $columns) ? $crud->colRename("id_phase", "Fase do processo") : null;
        in_array("invoice_number", $columns) ? $crud->colRename("invoice_number", "NF") : null;
        in_array("online_order_number", $columns) ? $crud->colRename("online_order_number", "ORIGEM") : null;
        in_array("bling_number", $columns) ? $crud->colRename("bling_number", "Nº Bling") : null;
        in_array("order_date", $columns) ? $crud->colRename("order_date", "Data do pedido") : null;
        in_array("expected_date", $columns) ? $crud->colRename("expected_date", "Data prevista") : null;
        in_array("isbn", $columns) ? $crud->colRename("isbn", "ISBN") : null;
        in_array("selling_price", $columns) ? $crud->colRename("selling_price", "Valor") : null;
        in_array("supplier_name", $columns) ? $crud->colRename("supplier_name", "Fornecedor") : null;
        in_array("purchase_date", $columns) ? $crud->colRename("purchase_date", "Data da compra") : null;
        in_array("id_delivery_address", $columns) ? $crud->colRename("id_delivery_address", "Endereço de entrega") : null;
        in_array("supplier_purchase_number", $columns) ? $crud->colRename("supplier_purchase_number", "Nº Compra fornecedor") : null;
        in_array("id_delivery_method", $columns) ? $crud->colRename("id_delivery_method", "Forma de envio") : null;
        in_array("tracking_code", $columns) ? $crud->colRename("tracking_code", "Código de rastreio") : null;
        in_array("collection_code", $columns) ? $crud->colRename("collection_code", "Código de coleta") : null;
        in_array("delivered_date", $columns) ? $crud->colRename("delivered_date", "Data de entrega") : null;
        in_array("ask_rating", $columns) ? $crud->colRename("ask_rating", "Pedir avaliação") : null;
        
        return [$crud, $columns];
    }

    private static function fieldsNotMandatory(PDOCrud $crud, array $columns)
    {
        \in_array("invoice_number", $columns) ? $crud->fieldNotMandatory("invoice_number") : null;
        \in_array("bling_number", $columns) ? $crud->fieldNotMandatory("bling_number") : null;
        \in_array("supplier_name", $columns) ? $crud->fieldNotMandatory("supplier_name") : null;
        \in_array("purchase_date", $columns) ? $crud->fieldNotMandatory("purchase_date") : null;
        \in_array("id_delivery_address", $columns) ? $crud->fieldNotMandatory("id_delivery_address") : null;
        \in_array("supplier_purchase_number", $columns) ? $crud->fieldNotMandatory("supplier_purchase_number") : null;
        \in_array("id_delivery_method", $columns) ? $crud->fieldNotMandatory("id_delivery_method") : null;
        \in_array("tracking_code", $columns) ? $crud->fieldNotMandatory("tracking_code") : null;
        \in_array("collection_code", $columns) ? $crud->fieldNotMandatory("collection_code") : null;
        \in_array("delivered_date", $columns) ? $crud->fieldNotMandatory("delivered_date") : null;
        \in_array("ask_rating", $columns) ? $crud->fieldNotMandatory("ask_rating") : null;

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

    private static function fieldBulkUpdate(PDOCrud $crud, array $columns)
    {
        $phases = Order::getBulkUpdateData($crud, 'phases', function($pair, $key, $val)
        {
            return $pair[$key];
        });

        if(\in_array('id_delivery_address', $columns)) $deliveryAddresses = Order::getBulkUpdateData($crud, 'delivery_addresses', function($pair, $key, $val)
        {
            return $pair[$val];
        });

        if(\in_array('id_delivery_method', $columns)) $deliveryMethods = Order::getBulkUpdateData($crud, 'delivery_methods', function($pair, $key, $val)
        {
            return $pair[$val];
        });

        $crud->bulkCrudUpdate("id_phase", "select", ['phase_key' => 'phase_val'], $phases);
        \in_array("invoice_number", $columns) ? $crud->bulkCrudUpdate("invoice_number", "text") : null;
        \in_array("bling_number", $columns) ? $crud->bulkCrudUpdate("bling_number", "text") : null;
        \in_array("supplier_name", $columns) ? $crud->bulkCrudUpdate("supplier_name", "text") : null;
        \in_array("purchase_date", $columns) ? $crud->bulkCrudUpdate("purchase_date", "date") : null;
        \in_array("id_delivery_address", $columns) ? $crud->bulkCrudUpdate("id_delivery_address", "select", ['deliveryaddress_key' => 'deliveryaddress_val'], $deliveryAddresses) : null;
        \in_array("supplier_purchase_number", $columns) ? $crud->bulkCrudUpdate("supplier_purchase_number", "text") : null;
        \in_array("id_delivery_method", $columns) ? $crud->bulkCrudUpdate("id_delivery_method", "select", ['deliverymethod_key' => 'deliverymethod_val'], $deliveryMethods) : null;
        \in_array("tracking_code", $columns) ? $crud->bulkCrudUpdate("tracking_code", "text") : null;
        \in_array("collection_code", $columns) ? $crud->bulkCrudUpdate("collection_code", "text") : null;
        \in_array("delivered_date", $columns) ? $crud->bulkCrudUpdate("delivered_date", "date") : null;
        \in_array("ask_rating", $columns) ? $crud->bulkCrudUpdate("ask_rating", "select", ['askrating_key' => 'askrating_val'], [
            [1, "Sim"],
            [0, "Não"]
        ]) : null;

        return $crud;
    }

    private static function fieldsFormatting(PDOCrud $crud, array $columns)
    {
        $db = $crud->getPDOModelObj();
        $sellercentrals = $db->select("sellercentrals");
        $phases = $db->select('phases');

        foreach($sellercentrals as $sel) {
            $crud->tableColFormatting("id_sellercentral", "replace", [$sel["id"] => $sel["name"]]);
        }
        \in_array("order_date", $columns) ? $crud->tableColFormatting("order_date", "date", ["format" => "d/m/Y"]) : null;
        \in_array("expected_date", $columns) ? $crud->tableColFormatting("expected_date", "date", ["format" => "d/m/Y"]) : null;
        \in_array("purchase_date", $columns) ? $crud->tableColFormatting("purchase_date", "date", ["format" => "d/m/Y"]) : null;
        \in_array("delivered_date", $columns) ? $crud->tableColFormatting("delivered_date", "date", ["format" => "d/m/Y"]) : null;

        return $crud;
    }

    private static function fieldsFiltering(PDOCrud $crud, string|null $phase)
    {
        if(isset($phase)) $crud->where('id_phase', $phase);

        return $crud;
    }

    private static function crudSettings(PDOCrud $crud)
    {
        $crud->setSettings("actionbtn", false);
        $crud->setSettings("checkboxCol", false);

        return $crud;
    }
}
