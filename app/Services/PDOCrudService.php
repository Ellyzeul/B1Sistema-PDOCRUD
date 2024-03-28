<?php namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;	
use \PDOCrud;

class PDOCrudService
{
	private array $columnsRename = [
		"id" => "Nº",
		"address_verified" => "Endereço arrumado",
		"ready_for_ship" => "Pronto p/ envio",
		"id_company" => "Empresa",
		"id_sellercentral" => "Canal de venda",
		"id_phase" => "Fase do processo",
		"invoice_number" => "NF",
		"online_order_number" => "ORIGEM",
		"bling_number" => "Nº Bling",
		"order_date" => "Data do pedido",
		"ship_date" => "Data para envio",
		"expected_date" => "Data prevista",
		"isbn" => "ISBN",
		"selling_price" => "Valor",
		"supplier_name" => "Fornecedor",
		"purchase_date" => "Data da compra",
		"id_delivery_address" => "Endereço de entrega",
		"supplier_purchase_number" => "Nº Compra fornecedor",
		"supplier_tracking_code" => "Código de rastreio fornecedor",
		"id_supplier_delivery_method" => "Forma de entrega fornecedor",
		"id_delivery_method" => "Forma de envio",
		"tracking_code" => "Código de rastreio",
		"collection_code" => "Código de coleta",
		"delivered_date" => "Data de entrega",
		"ask_rating" => "Pedir avaliação",
		"ready_to_6_2" => "Pronto para 6.2",
		"accepted" => "Aceito",
		"shipping_box_number" => "Nº da caixa",
		"cancel_invoice" => "NF cancelada",
		"weight" => "Peso", 

		"delivery_hub" => "Transportadora HUB", 
		"url" => "Monitoramento", 
		"courier_delivered_date" => "Chegada Courier", 
		"items" => "Itens na caixa", 
		"weight" => "Peso", 
		"total_cost" => "Valor da caixa", 
		"delivered_on_envia_com" => "Entregue na Envia.com", 
		"hub_ship_date" => "Data de saída do HUB", 
		"status" => "Status", 
	];
	private array $columnsPerPhase = [
		"id" => ["general", "Pré-0", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.11", "4.2", "4.3", "4.21", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado", "aceitar-fnac", "cancelar-nf"],
		"address_verified" => ["general", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado"],
		"ready_for_ship" => ["general", "não-enviado"],
		"id_company" => ["general", "Pré-0", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado", "aceitar-fnac", "cancelar-nf"],
		"id_sellercentral" => ["general", "Pré-0", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado", "aceitar-fnac", "cancelar-nf"],
		"id_phase" => ["general", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado", "cancelar-nf"],
		"invoice_number" => ["general", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "4.1", "4.11", "4.2", "4.3", "4.21", "cancelar-nf"],
		"online_order_number" => ["general", "Pré-0", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado", "aceitar-fnac", "cancelar-nf"],
		"bling_number" => ["general", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "4.1", "4.2", "4.3", "6.2", "6.21", "não-verificado", "não-enviado", "cancelar-nf"],
		"order_date" => ["general", "Pré-0", "2.1", "2.3", "3.1", "3.2", "4.1", "4.2", "4.3", "não-verificado", "não-enviado"],
		"ship_date" => ["general", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.2", "4.3", "não-verificado", "não-enviado", "aceitar-fnac"],
		"expected_date" => ["general", "Pré-0", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.11", "4.2", "4.3", "4.21", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado", "aceitar-fnac"],
		"isbn" => ["general", "Pré-0", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2", "4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado", "aceitar-fnac"],
		"selling_price" => ["general", "Pré-0", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.1", "2.3", "3.1", "3.2", "4.1", "4.2", "4.3", "não-enviado", "aceitar-fnac"],
		"supplier_name" => ["general", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "3.1", "3.2"],
		"purchase_date" => ["general", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "3.1", "3.2"],
		"id_delivery_address" => ["general", "2.3", "3.1", "3.2"],
		"supplier_purchase_number" => ["general", "2.1", "2.3", "3.1", "3.2"],
		"supplier_tracking_code" => ["general", "2.1", "2.3", "3.1", "3.2"],
		"id_supplier_delivery_method" => ["general", "2.1", "2.3", "3.1", "3.2"],
		"id_delivery_method" => ["general", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "4.1", "4.2", "4.3", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado"],
		"tracking_code" => ["general", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "4.1", "4.11", "4.2", "4.3", "4.21", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6", "não-verificado", "não-enviado"],
		"collection_code" => ["general", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8"],
		"delivered_date" => ["general", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
		"ask_rating" => ["general", "0.0", "1.1", "1.2" ,"1.3", "1.4", "1.5", "2.0", "2.1", "2.11", "2.2", "2.3", "2.4", "2.41", "2.5", "2.6", "2.7", "2.8", "2.9", "5.1", "5.2", "5.3", "5.4", "5.5", "6.1", "6.2", "6.21", "7.0", "8.1", "8.12", "8.13", "8.2", "8.3", "8.4", "8.5", "8.6"],
		"ready_to_6_2" => ["general", "6.1"],
		"accepted" => ["general", "aceitar-fnac"],
		"shipping_box_number" => ["general", "2.9", "4.1", "4.2", "5.1"],
		"cancel_invoice" => ["cancelar-nf"],
		"weight" => ["general", "2.9", "4.1", "4.11", "4.2", "4.3", "4.21"],

		"delivery_hub" => ["4.11", "4.21"], 
		"url" => ["4.11", "4.21"], 
		"courier_delivered_date" => ["4.11", "4.21"], 
		"items" => ["4.11", "4.21"], 
		"total_cost" => ["4.11", "4.21"], 
		"delivered_on_envia_com" => ["4.11", "4.21"], 
		"hub_ship_date" => ["4.11", "4.21"], 
		"status" => ["4.11", "4.21"], 
	];

	public function getHTML(Request $request)
	{
		$phase = $request->input('phase') ?? null;
		$orderNumber = $request->input('origem') ?? null;

		if($phase === '6.1') $this->updateReadyTo6_2();

		$crud = new PDOCrud();
		[$crud, $columns] = $this->setFields($crud, $phase);
		$crud = $this->fieldsNotMandatory($crud, $columns);
		$crud = $this->fieldBulkUpdate($crud, $columns, $phase);
		$crud = $this->fieldsFormatting($crud, $columns);
		$crud = $this->fieldsFiltering($crud, $phase, $orderNumber);
		$crud = $this->crudSettings($crud, $phase);

		$crud->dbOrderBy("id desc");

		$response = [
			'html' => $crud->dbTable(!\in_array($phase, ['4.11', '4.21']) ? "order_control" : "shipping_box")->render()
		];

		return $response;
	}

	public function getColumnsNames()
	{
			return $this->columnsRename;
	}

	private function updateReadyTo6_2()
	{
		Order::where('id_phase', '6.1')->update([
			'ready_to_6_2' => DB::raw('IF(DATEDIFF(NOW(), delivered_date) < 5, "Não", "Sim")')
		]);
	}

	private function setFields(PDOCrud $crud, ?string $phase)
	{
		$columns = [];
		foreach($this->columnsPerPhase as $column => $validPhases) {
			if($phase != null ? \in_array($phase, $validPhases) : \in_array('general', $validPhases)) {
				array_push($columns, $column);
			}
		}
		$crud->crudTableCol($columns);

		foreach($columns as $column) {
			$crud->colRename($column, $this->columnsRename[$column]);
		}

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
		\in_array("supplier_tracking_code", $columns) ? $crud->fieldNotMandatory("supplier_tracking_code") : null;
		\in_array("id_supplier_delivery_method", $columns) ? $crud->fieldNotMandatory("id_supplier_delivery_method") : null;
		\in_array("id_delivery_method", $columns) ? $crud->fieldNotMandatory("id_delivery_method") : null;
		\in_array("tracking_code", $columns) ? $crud->fieldNotMandatory("tracking_code") : null;
		\in_array("collection_code", $columns) ? $crud->fieldNotMandatory("collection_code") : null;
		\in_array("delivered_date", $columns) ? $crud->fieldNotMandatory("delivered_date") : null;
		\in_array("ask_rating", $columns) ? $crud->fieldNotMandatory("ask_rating") : null;

		return $crud;
	}

	private function getBulkUpdateData(PDOCrud $crud, string $table, \Closure $nameMaker, string $key='id', string $val='name', array $idsToExclude = [])
	{
		$db = $crud->getPDOModelObj();
		$results = $db->select($table);

		$data = [];
		foreach($results as $pair) {
			if(in_array($pair[$key], $idsToExclude)) continue;

			array_push($data, [
				$pair[$key],
				$nameMaker($pair, $key, $val)
			]);
		}

		return $data;
	}

	private function fieldBulkUpdate(PDOCrud $crud, array $columns, ?string $phase)
	{
		$phases = $this->getBulkUpdateData($crud, 'phases', function($pair, $key, $val)
		{
			return $pair[$key];
		}, idsToExclude: ['4.11', '4.21']);

		if(\in_array('id_delivery_address', $columns)) $deliveryAddresses = $this->getBulkUpdateData($crud, 'delivery_addresses', function($pair, $key, $val)
		{
			return "$pair[$key] - $pair[$val]";
		});

		if(\in_array('id_delivery_method', $columns)) $deliveryMethods = $this->getBulkUpdateData($crud, 'delivery_methods', function($pair, $key, $val)
		{
			return "$pair[$key] - $pair[$val]";
		});

		if(\in_array('id_supplier_delivery_method', $columns)) $supplierDeliveryMethods = $this->getBulkUpdateData($crud, 'supplier_delivery_methods', function($pair, $key, $val)
		{
			return "$pair[$key] - $pair[$val]";
		});

		\in_array("address_verified", $columns) ? $crud->bulkCrudUpdate("address_verified", "number") : null;
		\in_array("ready_for_ship", $columns) ? $crud->bulkCrudUpdate("ready_for_ship", "number") : null;
		if(!\in_array($phase, ["aceitar-fnac", "4.11", "4.21"])) $crud->bulkCrudUpdate("id_phase", "select", ['phase_key' => 'phase_val'], $phases);
		\in_array("invoice_number", $columns) ? $crud->bulkCrudUpdate("invoice_number", "text") : null;
		\in_array("bling_number", $columns) ? $crud->bulkCrudUpdate("bling_number", "text") : null;
		\in_array("supplier_name", $columns) ? $crud->bulkCrudUpdate("supplier_name", "text") : null;
		\in_array("purchase_date", $columns) ? $crud->bulkCrudUpdate("purchase_date", "date") : null;
		\in_array("id_delivery_address", $columns) ? $crud->bulkCrudUpdate("id_delivery_address", "select", ['deliveryaddress_key' => 'deliveryaddress_val'], $deliveryAddresses) : null;
		\in_array("supplier_purchase_number", $columns) ? $crud->bulkCrudUpdate("supplier_purchase_number", "text") : null;
		\in_array("supplier_tracking_code", $columns) ? $crud->bulkCrudUpdate("supplier_tracking_code", "text") : null;
		\in_array("id_supplier_delivery_method", $columns) ? $crud->bulkCrudUpdate("id_supplier_delivery_method", "select", ['supplierdeliverymethod_key' => 'supplierdeliverymethod_val'], $supplierDeliveryMethods) : null;
		\in_array("id_delivery_method", $columns) ? $crud->bulkCrudUpdate("id_delivery_method", "select", ['deliverymethod_key' => 'deliverymethod_val'], $deliveryMethods) : null;
		\in_array("tracking_code", $columns) ? $crud->bulkCrudUpdate("tracking_code", "text") : null;
		\in_array("collection_code", $columns) ? $crud->bulkCrudUpdate("collection_code", "text") : null;
		\in_array("delivered_date", $columns) ? $crud->bulkCrudUpdate("delivered_date", "date") : null;
		\in_array("cancel_invoice", $columns) ? $crud->bulkCrudUpdate("cancel_invoice", "number") : null;
		\in_array("weight", $columns) ? $crud->bulkCrudUpdate("weight", "number") : null;
		\in_array("ask_rating", $columns) ? $crud->bulkCrudUpdate("ask_rating", "select", ['askrating_key' => 'askrating_val'], [
			[1, "Sim"],
			[0, "Não"],
			[2, "1ª Enviada"],
			[3, "2ª Enviada"],
		]) : null;
		\in_array("accepted", $columns) ? $crud->bulkCrudUpdate("accepted", "select", ['accepted_key' => 'accepted_val'], [
			[0, "Não"],
			[1, "Sim"],
			[2, "Verificando"],
			[3, "Aceito"],
		]) : null;
		\in_array("shipping_box_number", $columns) ? $crud->bulkCrudUpdate("shipping_box_number", "text") : null;

		\in_array("delivery_hub", $columns) ? $crud->bulkCrudUpdate("delivery_hub", "text") : null;
		\in_array("url", $columns) ? $crud->bulkCrudUpdate("url", "text") : null;
		\in_array("courier_delivered_date", $columns) ? $crud->bulkCrudUpdate("courier_delivered_date", "date") : null;
		\in_array("items", $columns) ? $crud->bulkCrudUpdate("items", "number") : null;
		\in_array("weight", $columns) ? $crud->bulkCrudUpdate("weight", "number") : null;
		\in_array("total_cost", $columns) ? $crud->bulkCrudUpdate("total_cost", "number") : null;
		\in_array("delivered_on_envia_com", $columns) ? $crud->bulkCrudUpdate("delivered_on_envia_com", "select", ['delivered_on_envia_com_key' => 'delivered_on_envia_com_val'], [
			[0, "Não"],
			[1, "Sim"],
		]) : null;
		\in_array("hub_ship_date", $columns) ? $crud->bulkCrudUpdate("hub_ship_date", "date") : null;
		\in_array("status", $columns) ? $crud->bulkCrudUpdate("status", "select", ['status_key' => 'status_val'], [
			[0, "Em aberto"],
			[1, "Encerrado"],
		]) : null;
		if(\in_array($phase, ["4.11", "4.21"])) \in_array("expected_date", $columns) ? $crud->bulkCrudUpdate("expected_date", "date") : null;

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
		\in_array("ship_date", $columns) ? $crud->tableColFormatting("ship_date", "date", ["format" => "d/m/Y H:i:s"]) : null;
		\in_array("expected_date", $columns) ? $crud->tableColFormatting("expected_date", "date", ["format" => "d/m/Y"]) : null;
		\in_array("purchase_date", $columns) ? $crud->tableColFormatting("purchase_date", "date", ["format" => "d/m/Y"]) : null;
		\in_array("delivered_date", $columns) ? $crud->tableColFormatting("delivered_date", "date", ["format" => "d/m/Y"]) : null;

		return $crud;
	}

	private function fieldsFiltering(PDOCrud $crud, ?string $phase, ?string $orderNumber)
	{
		if($phase == "não-verificado") {
			$crud->where('address_verified', 0);
			$crud->where('accepted', 3);
			$crud->where('id_phase', 8, "<");

			return $crud;
		}
		if($phase == "não-enviado") return $this->filterByUnverified($crud, 'ready_for_ship');
		if($phase == "aceitar-fnac") {
			$crud->where('accepted', 2, '<=');

			return $crud;
		}
		if($phase == "cancelar-nf") {
			$crud->where('id_phase', 8.2, ">=");
			$crud->where('cancel_invoice', 0);

			return $crud;
		}
		if(is_numeric($phase) && !\in_array($phase, ['4.11', '4.21'])) $crud->where('id_phase', $phase);

		if(isset($orderNumber)) $crud->where('online_order_number', $orderNumber);

		return $crud;
	}

	private function filterByUnverified(PDOCrud $crud, string $columnName)
	{
		$crud->where($columnName, 0);
		return $crud;
	}

	private function crudSettings(PDOCrud $crud, ?string $phase)
	{
		$crud->setSettings("actionbtn", false);
		$crud->setSettings("checkboxCol", false);
		if(!isset($phase)) $crud->setSettings("excelBtn", false);

		return $crud;
	}
}
