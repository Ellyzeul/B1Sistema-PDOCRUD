<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FileUpload extends Model
{
	use HasFactory;

	private static array $orderUpdatable = [
		"id" => false,
		"id_company" => false,
		"id_sellercentral" => false,
		"id_phase" => true,
		"invoice_number" => true,
		"online_order_number" => false,
		"bling_number" => true,
		"order_date" => false,
		"expected_date" => false,
		"isbn" => false,
		"selling_price" => false,
		"supplier_name" => true,
		"purchase_date" => true,
		"id_delivery_address" => true,
		"supplier_purchase_number" => true,
		"id_delivery_method" => true,
		"tracking_code" => true,
		"collection_code" => true,
		"delivered_date" => true,
		"ask_rating" => true,
		"address_verified" => true,
	];

	public function orderUpdate(array $data)
	{
		$responses = [];

		foreach($data as $registry) {
			array_push($responses, $this->updateRegistry($registry));
		}

		return $responses;
	}

	public function orderAmazonInsert(array $data)
	{
		$responses = [];

		foreach($data as $registry) {
			array_push($responses, $this->insertAmazonRegistry($registry));
		}

		return $responses;
	}

	public function orderAddressInsert(array $data)
	{
		$treatedData = array_map(function($registry) {
			if(isset($registry['expected_date'])) $registry = $this->treatExpectedDate($registry);
			return $registry;
		}, $data);
		$fields = count($treatedData) > 0
			? array_keys($treatedData[0])
			: [];

		DB::table('order_addresses')
			->upsert(
				$treatedData,
				['online_order_number'],
				$fields
			);
		
		return [
			'message' => 'Endereços inseridos com sucesso!'
		];
	}

	public function orderEstanteInsert(array $data)
	{
		return;
	}

	private function updateRegistry(array $registry)
	{
		$id = $registry["id"];
		$onlineOrderNumber = $registry["online_order_number"];

		$registry = $this->treatUpdateDate($registry);
		DB::table("order_control")
			->where("id", $id)
			->where("online_order_number", $onlineOrderNumber)
			->update($registry);

		return "Pedido $onlineOrderNumber atualizado";
	}

	private function treatUpdateDate(array $registry)
	{
		if(isset($registry["purchase_date"])) $registry["purchase_date"] = $this->getPlainDate($registry["purchase_date"]);
		if(isset($registry["delivered_date"])) $registry["delivered_date"] = $this->getPlainDate($registry["delivered_date"]);

		return $registry;
	}

	private function getPlainDate(string $date)
	{
		return \explode("T", $date)[0];
	}

	private function insertAmazonRegistry(array $registry)
	{
		$onlineOrderNumber = $registry["online_order_number"];
		$registry = $this->treatInsertAmazonDate($registry);
		DB::table("order_control")
			->insert($registry);
		
		return "Pedido $onlineOrderNumber inserido";
	}

	private function treatInsertAmazonDate(array $registry)
	{
		$registry = $this->treatOrderDate($registry);
		$registry = $this->treatExpectedDate($registry);
		
		return $registry;
	}

	private function treatOrderDate(array $registry)
	{
		$registry["order_date"] = date("Y-m-d", strtotime($registry["order_date"]));

		return $registry;
	}

	private function treatExpectedDate(array $registry)
	{
		$expectedDate = strtotime($registry["expected_date"]);
		$deliveryHour = intval(date("H", $expectedDate));
		$subtractDay = ($deliveryHour > 0) && ($deliveryHour < 7);
		$registry["expected_date"] = $subtractDay
			? date("Y-m-d", strtotime("-1 day", $expectedDate))
			: date("Y-m-d", $expectedDate);
		
		return $registry;
	}
}
