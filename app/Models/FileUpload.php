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
		$orderData = array_map(fn ($registry) => [
			'id_company' => $registry['id_company'], 
			'id_sellercentral' => $this->getAmazonIdSellercentral($registry['sellercentral']), 
			'online_order_number' => $registry['online_order_number'], 
			'order_date' => date('Y-m-d', strtotime($registry['order_date'])), 
			'ship_date' => $this->treatAmazonDatetime($registry['ship_date']), 
			'expected_date' => $this->treatExpectedDate($registry['expected_date']), 
			'isbn' => explode("_", $registry['sku'])[1], 
			'selling_price' => $registry['item_price'], 
		], $data);
		
		$addressData = array_map(fn ($registry) => [
			'online_order_number' => $registry['online_order_number'], 
			'buyer_name' => $registry['buyer_name'], 
			'buyer_email' => $registry['buyer_email'], 
			'buyer_phone' => $registry['buyer_phone'], 
			'recipient_name' => $registry['recipient_name'], 
			'ship_phone' => $registry['ship_phone'], 
			'address_1' => $registry['address_1'], 
			'address_2' => $registry['address_2'], 
			'address_3' => $registry['address_3'], 
			'county' => $registry['county'], 
			'city' => $registry['city'], 
			'state' => $registry['state'], 
			'postal_code' => $registry['postal_code'], 
			'country' => $registry['country'], 
			'price' => $registry['item_price'], 
			'item_tax' => $registry['item_tax'], 
			'freight' => $registry['freight_price'], 
			'freight_tax' => $registry['freight_tax'], 
		], $data);

		$this->orderDataInsert($orderData);
		$this->orderAddressInsert($addressData);

		return;
	}

	private function getAmazonIdSellercentral(string $sellercentral)
	{
		if($sellercentral === "Amazon.com.br") return 1;
		if($sellercentral === "Amazon.ca") return 2;
		if($sellercentral === "Amazon.co.uk") return 3;
		if($sellercentral === "Amazon.com") return 4;

		throw("Invalid sellercentral: " . $sellercentral);
	}

	private function treatAmazonDatetime(string $date)
	{
		$deliveryHour = intval(date("H", $date));
		$subtractDay = ($deliveryHour > 0) && ($deliveryHour < 7);
		$treated = $subtractDay
			? date("Y-m-d", strtotime("-1 day", $date))
			: date("Y-m-d", $date);
		
		return $treated;
	}

	public function orderNuvemshopInsert(array $data)
	{
		$orderData = array_map(fn ($registry) => [
			'id_company' => 0, 
			'id_sellercentral' => $this->getNuvemshopIdSellercentral($registry['currency']), 
			'online_order_number' => $registry['online_order_number'], 
			'order_date' => date('Y-m-d', strtotime($registry['order_date'])), 
			'ship_date' => date('Y-m-d', strtotime($registry['ship_date'])), 
			'expected_date' => date('Y-m-d', strtotime($registry['expected_date'])), 
			'isbn' => explode("_", $registry['sku'])[1], 
			'selling_price' => $registry['price'] - $registry['discount'], 
		], $data);
		
		$addressData = array_map(fn ($registry) => [
			'online_order_number' => $registry['online_order_number'], 
			'buyer_name' => $registry['buyer_name'], 
			'buyer_email' => $registry['buyer_email'], 
			'buyer_phone' => $registry['buyer_phone'], 
			'recipient_name' => $registry['recipient_name'], 
			'ship_phone' => $registry['ship_phone'], 
			'address_1' => $registry['address_1'], 
			'address_2' => $registry['address_2'], 
			'county' => $registry['county'], 
			'city' => $registry['city'], 
			'state' => $registry['state'], 
			'postal_code' => $registry['postal_code'], 
			'country' => $registry['country'], 
			'price' => $registry['price'], 
			'freight' => $registry['freight'], 
		], $data);

		$this->orderDataInsert($orderData);
		$this->orderAddressInsert($addressData);
	}

	private function getNuvemshopIdSellercentral(string $currency)
	{
		if($currency === "BRL") return 5;

		throw("Moeda inválida");
	}

	public function orderEstanteInsert(array $data)
	{
		$orderData = array_map(fn ($registry) => [
			'id_company' => 0, 
			'id_sellercentral' => 6, 
			'online_order_number' => $registry['online_order_number'], 
			'order_date' => date('Y-m-d', strtotime($registry['order_date'])), 
			'expected_date' => date('Y-m-d', strtotime($registry['expected_date'])), 
			'isbn' => $registry['isbn'], 
			'selling_price' => $registry['price'], 
		], $data);

		$addressData = array_map(fn ($registry) => [
			'online_order_number' => $registry['online_order_number'], 
			'recipient_name' => $registry['recipient_name'], 
			'address_1' => $registry['address_1'], 
			'address_2' => $registry['address_2'], 
			'address_3' => $registry['address_2'], 
			'county' => $registry['county'], 
			'city' => $registry['city'], 
			'state' => $registry['state'], 
			'postal_code' => $registry['postal_code'], 
			'freight' => $registry['freight'], 
			'item_tax' => $registry['item_tax'], 
			'price' => $registry['price'], 
		], $data);

		$this->orderDataInsert($orderData);
		$this->orderAddressInsert($addressData);

		return [
			'message' => 'Pedidos da Estante Virtual inseridos com sucesso!'
		];
	}

	private function orderDataInsert(array $data)
	{
		DB::table("order_control")
			->insert($data);
	}

	private function orderAddressInsert(array $data)
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
}
