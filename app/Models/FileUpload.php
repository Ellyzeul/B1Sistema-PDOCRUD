<?php

namespace App\Models;

use App\Services\ThirdParty\Bling;
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
			'ship_date' => $this->treatAmazonDatetime($registry['ship_date']) . ' 23:59:59', 
			'expected_date' => $this->treatAmazonDatetime($registry['expected_date']), 
			'isbn' => explode("_", $registry['sku'])[1], 
			'selling_price' => $registry['item_price'], 
			'is_business_order' => $registry['is_business_order'] === "true" ? 1 : 0, 
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
			'expected_date' => $this->treatAmazonDatetime($registry['expected_date']), 
			'delivery_instructions' => $registry['delivery_instructions'], 
		], $data);

		$this->postAmazonOrderOnBling($addressData, $orderData);

		$this->orderAddressInsert($addressData);
		$this->orderDataInsert($orderData);

		return;
	}

	private function getAmazonIdSellercentral(string $sellercentral)
	{
		if($sellercentral === "Amazon.com.br") return 1;
		if($sellercentral === "Amazon.ca") return 2;
		if($sellercentral === "Amazon.co.uk") return 3;
		if($sellercentral === "Amazon.com") return 4;
		if($sellercentral === "Amazon.es") return 12;

		throw("Invalid sellercentral: " . $sellercentral);
	}

	private function treatAmazonDatetime(string $date)
	{
		$deliveryHour = intval(date("H", strtotime($date)));
		$subtractDay = ($deliveryHour > 0) && ($deliveryHour < 7);
		$treated = $subtractDay
			? date("Y-m-d", strtotime("-1 day", strtotime($date)))
			: date("Y-m-d", strtotime($date));
		
		return $treated;
	}

	public function orderNuvemshopInsert(array $data)
	{
		$orderData = array_map(fn ($registry) => [
			'id_company' => 0, 
			'id_sellercentral' => $this->getNuvemshopIdSellercentral($registry['currency']), 
			'online_order_number' => $registry['online_order_number'], 
			'order_date' => date('Y-m-d', strtotime($registry['order_date'])), 
			'ship_date' => date('Y-m-d 23:59:59', strtotime($registry['ship_date'])), 
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
			'expected_date' => date('Y-m-d', strtotime($registry['expected_date'])), 
		], $data);

		$this->orderAddressInsert($addressData);
		$this->orderDataInsert($orderData);
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
			'ship_date' => date('Y-m-d H:i:s', strtotime($registry['ship_date'])), 
			'isbn' => $registry['isbn'], 
			'selling_price' => $registry['price'], 
		], $data);

		$addressData = array_map(fn ($registry) => [
			'online_order_number' => $registry['online_order_number'], 
			'buyer_name' => $registry['buyer_name'], 
			'buyer_email' => $registry['buyer_email'], 
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
			'expected_date' => date('Y-m-d ', strtotime($registry['expected_date'])), 
		], $data);

		$this->orderAddressInsert($addressData);
		$this->orderDataInsert($orderData);

		return [
			'message' => 'Pedidos da Estante Virtual inseridos com sucesso!'
		];
	}

	public function orderAlibrisInsert(array $data)
	{
		$orderData = array_map(fn ($registry) => [
			'id_company' => 0, 
			'id_sellercentral' => 7, 
			'online_order_number' => $registry['online_order_number'], 
			'order_date' => date('Y-m-d', strtotime($registry['order_date'])), 
			'ship_date' => date('Y-m-d 23:59:59', strtotime("+2 day", strtotime($registry['order_date']))), 
			'expected_date' => date('Y-m-d', strtotime($registry['expected_date'])), 
			'isbn' => $registry['isbn'], 
			'selling_price' => $registry['price'], 
		], $data);

		$addressData = array_map(fn ($registry) => [
			'online_order_number' => $registry['online_order_number'], 
			'buyer_email' => $registry['buyer_email'], 
			'recipient_name' => 'Alibris Distribution Center', 
			'address_1' => '708 Spice Islands Dr.', 
			'city' => 'Sparks', 
			'state' => 'NV', 
			'postal_code' => '89431-7101', 
			'price' => $registry['price'], 
			'expected_date' => date('Y-m-d', strtotime($registry['expected_date'])), 
		], $data);

		$this->orderAddressInsert($addressData);
		$this->orderDataInsert($orderData);

		return [
			'message' => 'Pedidos da Alibris inseridos com sucesso!'
		];
	}

	public function orderFNACInsert(array $data)
	{
		$orderData = array_map(function($registry) {
			$treated = [
				'id_company' => 0, 
				'id_sellercentral' => 8, 
				'accepted' => $registry['status'] === 'validação pendente' ? 2 : 3, 
				'online_order_number' => $registry['online_order_number'], 
				'order_date' => date('Y-m-d', strtotime($registry['order_date'])), 
				'isbn' => $registry['isbn'], 
				'selling_price' => $registry['price'], 
			];
			if($registry['status'] === 'validação pendente') $treated['id_phase'] = 'Pré-0';
			if(isset($registry['ship_date'])) 
				$treated['ship_date'] = date('Y-m-d 23:59:59', strtotime($registry['ship_date']));
			if(isset($registry['expected_date'])) 
				$treated['expected_date'] = date('Y-m-d', strtotime($registry['expected_date']));
			
			return $treated;
		}, $data);

		$addressData = array_map(function($registry) {
			$treated = [
				'online_order_number' => $registry['online_order_number'], 
				'buyer_name' => "{$registry['recipient_name']} {$registry['recipient_surname']}", 
				'recipient_name' => "{$registry['recipient_name']} {$registry['recipient_surname']}", 
				'ship_phone' => $registry['ship_phone'], 
				'price' => $registry['price'], 
				'freight' => $registry['freight'], 
				'cpf_cnpj' => $registry['nif'], 
			];
			$treated = $this->setPropertyIfExists($registry, $treated, 'address_1');
			$treated = $this->setPropertyIfExists($registry, $treated, 'address_2');
			$treated = $this->setPropertyIfExists($registry, $treated, 'address_3');
			$treated = $this->setPropertyIfExists($registry, $treated, 'city');
			$treated = $this->setPropertyIfExists($registry, $treated, 'state');
			$treated = $this->setPropertyIfExists($registry, $treated, 'postal_code');
			$treated = $this->setPropertyIfExists($registry, $treated, 'country');

			return $treated;
		}, $data);

		$this->orderAddressInsert($addressData);

		$firstInserts = [];
		$secondInserts = [];
		foreach($orderData as $registry) {
			if($registry['accepted'] === 2) {
				array_push($firstInserts, $registry);
				continue;
			}

			array_push($secondInserts, $registry);
		}

		$this->orderDataInsert($firstInserts);
		$this->handleFNACSecondInserts($secondInserts);
	}

	private function setPropertyIfExists(array $registry, array $treated, string $key)
	{
		$treated[$key] = $registry[$key] ?? null;

		return $treated;
	}

	private function handleFNACSecondInserts(array $data)
	{
		if(count($data) === 0) return;

		$toInsert = [];
		foreach($data as $registry) {
			$dbRegistry = DB::table('order_control')
				->where('online_order_number', $registry['online_order_number'])
				->where('isbn', $registry['isbn']);

			$registryExists = $dbRegistry->exists();
			
			if(!$registryExists) {
				array_push($toInsert, $registry);
				continue;
			}

			$dbRegistry->update($registry);
		}

		$this->orderDataInsert($toInsert);
	}

	private function orderDataInsert(array $data)
	{
		DB::table("order_control")
			->insert($data);
	}

	private function orderAddressInsert(array $data)
	{
		$fields = count($data) > 0
			? array_keys($data[0])
			: [];

		DB::table('order_addresses')
			->upsert(
				$data,
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

	private function postAmazonOrderOnBling(array $addressData, array $orderData)
	{
		$addressesFormatted = [];
		$orders = [];
		$sellecentralsToPost = [3, 12];
		$countries = ['PT' => 'PORTUGAL', 'ES' => 'ESPANHA', 'UK' => 'REINO UNIDO', 'GB' => 'REINO UNIDO'];
		$skuPrefixes = ['PT-0' => 'SEL', 'ES-0' => 'SELESP', 'UK-0' => 'SELUK', 'PT-1' => 'B1', 'ES-1' => 'B1ESP', 'UK-1' => 'B1UK', 'GB-1' => 'B1UK', 'DE-1' => 'B1ESP', 'IE-1' => 'B1ESP'];

		foreach($addressData as $address) {
			$country = $countries[$address['country']] ?? '';
			$freight = floatval($address['freight']);

			$addressesFormatted[$address['online_order_number']] = [
				'country' => $address['country'], 
				'numeroLoja' => $address['online_order_number'], 
				'dataPrevista' => $address['expected_date'], 
				'loja' => ['id' => 0], 
				'outrasDespesas' => 0, 
				'observacoes' => "Nº Pedido Loja: {$address['online_order_number']}", 
				'observacoesInternas' => null, 
				'desconto' => ['valor' => 0, 'unidade' => 'REAL'], 
				'transporte' => [
					'frete' => $freight, 
					'etiqueta' => [
						'nome' => $address['recipient_name'], 
						'endereco' => $address['address_1'], 
						'numero' => '', 
						'complemento' => $address['address_2'], 
						'municipio' => $address['city'], 
						'uf' => $address['country'] === 'BR' ? $address['state'] : 'EX', 
						'cep' => $address['postal_code'], 
						'bairro' => $address['county'], 
						'nomePais' => $country, 
						'email' => $address['buyer_email'], 
						'phone' => $address['ship_phone'], 
					]
				]
			];
		}

		foreach($orderData as $item) {
			if(!in_array($item['id_sellercentral'], $sellecentralsToPost)) continue;
			
			$addressesFormatted[$item['online_order_number']]['id_company'] = $item['id_company'];
			$addressesFormatted[$item['online_order_number']]['data'] = date('Y-m-d',strtotime($item['order_date']));
			$skuPrefix = $skuPrefixes[$addressesFormatted[$item['online_order_number']]['country'] . '-' . $item['id_company']];

			if(isset($addressesFormatted[$item['online_order_number']]['itens'])) {
				array_push($addressesFormatted[$item['online_order_number']]['itens'], [
					"descricao" => $item['isbn'],
          "codigo"  => $skuPrefix . '_' . $item['isbn'],
          "valor" => $item['selling_price'],
          "quantidade" => 1
				]);
			}
			else {
				$addressesFormatted[$item['online_order_number']]['itens'] = [[
					"descricao" => $item['isbn'],
          "codigo"  => $skuPrefix . '_' . $item['isbn'],
          "valor" => $item['selling_price'],
          "quantidade" => 1
				]];
			}
		}

		foreach($addressesFormatted as $order) {
			unset($order['country']);
			if(isset($order['itens'])) array_push($orders, $order);
		}

		$this->handlePostAmazonOrderOnBling($orders);

		return;
	}

	private function handlePostAmazonOrderOnBling(array $orders)
	{
		$b1Bling = new Bling(1);
		$blings = [
			0 => new Bling(0),
			1 => $b1Bling,
		];

		foreach($orders as $order) {
			$idCompany = $order['id_company'];
			unset($order['id_company']);
			$address = $order['transporte']['etiqueta'];
			
			$contact = ($blings[$idCompany] ?? $b1Bling)->postContact([
				"nome" => $address['nome'],
				"codigo" => null,
				"situacao" => "A",
				"numeroDocumento"=> null,
				"telefone"=> null,
				"celular"=> $address['phone'],
				"fantasia"=> null,
				"tipo"=> 'E',
				"indicadorIe"=> 9,
				"ie"=> null,
				"rg"=> null,
				"orgaoEmissor"=> null,
				"email"=> $address['email'],
				"endereco" => [
					"geral" => [
						"endereco" => $address['endereco'],
						"cep" => $address['cep'],
						"bairro" => $address['bairro'],
						"municipio" => $address['municipio'],
						"uf" => $address['uf'], 
						"numero" => $address['numero'],
						"complemento" => $address['complemento'], 
					],
					"cobranca" => [
						"endereco" => null,
						"cep" => null,
						"bairro" => null,
						"municipio" => null,
						"uf" => null,
						"numero" => null,
						"complemento" => null
					]
				],
				"vendedor" => ["id" => null],
				"dadosAdicionais" => [
					"dataNascimento" => null,
					"sexo" => null,
					"naturalidade" => null
				],
				"financeiro" => [
					"limiteCredito" => 0,
					"condicaoPagamento" => null,
					"categoria" => ["id" => null]
				],
				"pais" => ["nome" => $address['nomePais']],
				"tiposContato" => [[
					"id" => 1431605062, 
					"descricao" => "Cliente"
				]],
				"pessoasContato" => [[
					"id" => 1431605062,
					"descricao" => "Cliente"
				]]
			]);

			unset($order['transporte']['etiqueta']['phone']);
			unset($order['transporte']['etiqueta']['email']);

			$order['contato'] = ['id' => $contact->id];

			$totalItems = count($order['itens']);
			for($i = 0; $i < $totalItems; $i++) {
				$product = ($blings[$idCompany] ?? $b1Bling)->getProductByCode($order['itens'][$i]['codigo']);

				if(!isset($product->id)) $product = ($blings[$idCompany] ?? $b1Bling)->postProduct([
					"nome" => $order['itens'][$i]['descricao'],
					"codigo" => $order['itens'][$i]['codigo'],
					"unidade" => "UN",
					"tipo" => "P",
					"situacao" => "A",
					"preco" => $order['itens'][$i]['valor'],
					"formato" => "S",
					"gtin" => $order['itens'][$i]['descricao'],
					"gtinEmbalagem" => $order['itens'][$i]['descricao'],
					"tributacao" => [
						"origem" => 0,
						"ncm" => "4901.99.00",
						"cest"=> "28.064.00"
					]
				]);

				$order['itens'][$i]['produto'] = ['id' => $product->id];
			}

			($blings[$idCompany] ?? $b1Bling)->postOrder($order);
		}

		return;
	}
}
