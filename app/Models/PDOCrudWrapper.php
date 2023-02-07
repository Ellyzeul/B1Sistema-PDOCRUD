<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \PDOCrud;

class PDOCrudWrapper extends Model
{
    use HasFactory;
    
    private array $columnsRename = [
        "id" => "Nº",
        "address_verified" => "Endereço verificado",
        "id_company" => "Empresa",
        "id_sellercentral" => "Canal de Venda",
        "id_phase" => "Fase do processo",
        "invoice_number" => "NF",
        "online_order_number" => "ORIGEM",
        "bling_number" => "Nº Bling",
        "order_date" => "Data do pedido",
        "expected_date" => "Data prevista",
        "isbn" => "ISBN",
        "selling_price" => "Valor",
        "supplier_name" => "Fornecedor",
        "purchase_date" => "Data da compra",
        "id_delivery_address" => "Endereço de entrega",
        "supplier_purchase_number" => "Nº Compra fornecedor",
        "id_delivery_method" => "Forma de envio",
        "tracking_code" => "Código de rastreio",
        "collection_code" => "Código de coleta",
        "delivered_date" => "Data de entrega",
        "ask_rating" => "Pedir avaliação",
        "ready_to_6_2" => "Pronto para 6.2",
    ];
    private array $columnsPerPhase = [
        "id" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "address_verified" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "id_company" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "id_sellercentral" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "id_phase" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "invoice_number" => ["2.4", "2.5", "2.6", "2.7", "2.8", "2.9"],
        "online_order_number" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "bling_number" => ["2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "6.2", "6.21", "não-verificado"],
        "order_date" => ["2.1", "2.3", "3.1", "3.2", "não-verificado"],
        "expected_date" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "isbn" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "selling_price" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.1", "2.3", "3.1", "3.2"],
        "supplier_name" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2"],
        "purchase_date" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "3.1", "3.2"],
        "id_delivery_address" => ["2.3", "3.1", "3.2"],
        "supplier_purchase_number" => ["2.1", "2.3", "3.1", "3.2"],
        "id_delivery_method" => ["2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "tracking_code" => ["2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado"],
        "collection_code" => ["2.4", "2.5", "2.6", "2.7", "2.8"],
        "delivered_date" => ["2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "ask_rating" => ["0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "2.9", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
        "ready_to_6_2" => ["6.1"],
    ];

    public function getHTML(string|null $phase)
    {
        $crud = new PDOCrud();
        [$crud, $columns] = $this->setFields($crud, $phase);
        $crud = $this->fieldsNotMandatory($crud, $columns);
        $crud = $this->fieldBulkUpdate($crud, $columns);
        $crud = $this->fieldsFormatting($crud, $columns);
        $crud = $this->fieldsFiltering($crud, $phase);
        $crud = $this->crudSettings($crud, $phase);

        $crud->dbOrderBy("id desc");

        $response = $crud->dbTable("order_control")->render();

        return $response;
    }

    public function getColumnsNames()
    {
        return $this->columnsRename;
    }

    private function setFields(PDOCrud $crud, string|null $phase)
    {
        $columns = [];
        foreach($this->columnsPerPhase as $column => $validPhases) {
            if($phase != null ? \in_array($phase, $validPhases) : true) {
                array_push($columns, $column);
            }
        }
        $crud->crudTableCol($columns);

        in_array("id", $columns) ? $crud->colRename("id", "Nº") : null;
        in_array("address_verified", $columns) ? $crud->colRename("address_verified", "Endereço arrumado") : null;
        in_array("id_company", $columns) ? $crud->colRename("id_company", "Empresa") : null;
        in_array("id_sellercentral", $columns) ? $crud->colRename("id_sellercentral", "Canal de venda") : null;
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
        in_array("ready_to_6_2", $columns) ? $crud->colRename("ready_to_6_2", "Pronto para 6.2") : null;
        
        return [$crud, $columns];
    }

    private function fieldsNotMandatory(PDOCrud $crud, array $columns)
    {
        \in_array("invoice_number", $columns) ? $crud->fieldNotMandatory("invoice_number") : null;
        \in_array("adrress_verified", $columns) ? $crud->fieldNotMandatory("adrress_verified") : null;
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

    private function getBulkUpdateData(PDOCrud $crud, string $table, \Closure $nameMaker, string $key='id', string $val='name')
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

    private function fieldBulkUpdate(PDOCrud $crud, array $columns)
    {
        $phases = $this->getBulkUpdateData($crud, 'phases', function($pair, $key, $val)
        {
            return $pair[$key];
        });

        if(\in_array('id_delivery_address', $columns)) $deliveryAddresses = $this->getBulkUpdateData($crud, 'delivery_addresses', function($pair, $key, $val)
        {
            return "$pair[$key] - $pair[$val]";
        });

        if(\in_array('id_delivery_method', $columns)) $deliveryMethods = $this->getBulkUpdateData($crud, 'delivery_methods', function($pair, $key, $val)
        {
            return "$pair[$key] - $pair[$val]";
        });

        \in_array("address_verified", $columns) ? $crud->bulkCrudUpdate("address_verified", "number") : null;
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
            [0, "Não"],
            [2, "1ª Enviada"],
            [3, "2ª Enviada"],
        ]) : null;

        return $crud;
    }

    private function fieldsFormatting(PDOCrud $crud, array $columns)
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

    private function fieldsFiltering(PDOCrud $crud, string|null $phase)
    {
        if($phase == "não-verificado") return $this->filterByUnverified($crud);
        if(is_numeric($phase)) $crud->where('id_phase', $phase);

        return $crud;
    }

    private function filterByUnverified(PDOCrud $crud)
    {
        $crud->where('address_verified', 0);
        return $crud;
    }

    private function crudSettings(PDOCrud $crud, string|null $phase)
    {
        $crud->setSettings("actionbtn", false);
        $crud->setSettings("checkboxCol", false);
        if(!isset($phase)) $crud->setSettings("excelBtn", false);

        return $crud;
    }
}
