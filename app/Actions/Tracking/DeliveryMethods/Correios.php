<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Actions\Tracking\Traits\DeliveryMethodsCommon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Date;

class Correios
{
  use DeliveryMethodsCommon;

  public function fetch(string $trackingCode)
	{	
		$today = Date::parse(date("Y-m-d H:i:s"));

		if(!$this->existsApiCredentialDB('correios')) $this->generateToken();

		$apikey = $this->readApiCredentialDB('correios');
		if(!isset($apikey->token)) $this->generateToken();
		
		$apikey = $this->readApiCredentialDB('correios');
		$expires_in = Date::parse($apikey->expiraEm);

		if((!$apikey->token) || $expires_in->diffInSeconds($today, false) > 1){
			$this->generateToken();
			$apikey = $this->readApiCredentialDB('correios');
		}

		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->withToken($apikey->token)
			->get("https://api.correios.com.br/srorastro/v1/objetos/$trackingCode");

		if(!isset($response['objetos'][0]['eventos'][0])) return [];
		$response = $response['objetos'][0]['eventos'][0];

		$street = $response['unidade']['endereco']['logradouro'] ?? "";
		$complement = $response['unidade']['endereco']['complemento'] ?? "";
		$number = $response['unidade']['endereco']['numero'] ?? "";
		$district = $response['unidade']['endereco']['bairro'] ?? "";
		$cep = $response['unidade']['endereco']['cep'] ?? "";
		$city = $response['unidade']['endereco']['cidade'] ?? "";
		$info = $response['detalhe'] ?? null;

		return [
			"status" => $response['descricao'],
			"last_update_date" => date('Y-m-d', strtotime(str_replace('/', '-', $response['dtHrCriado']))),
			"details" => "{$response['unidade']['tipo']}" 
				." - {$city}" 
				." - {$response['unidade']['endereco']['uf']} "
				."$street $complement $number $district $cep $info",

			"client_deadline" => $response['dtLimiteRetirada'] ?? null,
		];
	}

	public function consultPriceAndShipping(string $originZipCode, string $destinyZipCode, string|null $weight)
	{
		$today = Date::parse(date('Y-m-d H:i:s'));

		if(!$this->existsApiCredentialDB('correios')) $this->generateToken();

		$apikey = $this->readApiCredentialDB('correios');
		if(!isset($apikey->token)) $this->generateToken();

		$apikey = $this->readApiCredentialDB('correios');
		$expires_in = Date::parse($apikey->expiraEm);

		if((!$apikey->token) || $expires_in->diffInSeconds($today) > 1) {
			$this->generateToken();
			$apikey = $this->readApiCredentialDB('correios');
		} 

		$services = [
			'04227' => 'Mini Envios', 
			'03298' => 'PAC', 
			'03220' => 'Sedex', 
			'03204' => 'Sedex Hoje', 
		];

		$shipping_response = $this->fetchShipping($apikey->token, $originZipCode, $destinyZipCode);
		$price_response = $this->fetchPrice($apikey->token, $originZipCode, $destinyZipCode, $weight);

		if(!isset($shipping_response) || !isset($price_response)) return [];
		
		$responseMapped = array_map(fn($shipping, $price) => (object) [
			'name' => $services[$shipping['coProduto']], 
			'expected_deadline' => $shipping['prazoEntrega'] ?? null, 
			'expected_date' => isset($shipping['dataMaxima']) 
				? date('d/m/Y', strtotime($shipping['dataMaxima'])) 
				: $date = null,
			'shipping_error_msg' => $shipping['txErro'] ?? null,
			'price' => $price['pcFinal'] ?? null,
			'price_error_msg' => $price['txErro'] ?? null
		], $shipping_response->json(), $price_response->json());

		$response = array_reduce($responseMapped, function($acc, $cur) {
			return array_merge($acc ?? [], $cur);
		});

		return $response;
	}

	public function consultPostalCode(string $postalCode)
	{
		$today = Date::parse(date('Y-m-d H:i:s'));

		if(!$this->existsApiCredentialDB('correios')) $this->generateToken();

		$apikey = $this->readApiCredentialDB('correios');
		if(!isset($apikey->token)) $this->generateToken();

		$apikey = $this->readApiCredentialDB('correios');
		$expires_in = Date::parse($apikey->expiraEm);

		if((!$apikey->token) || $expires_in->diffInSeconds($today) > 1) {
			$this->generateToken();
			$apikey = $this->readApiCredentialDB('correios');
		} 

		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->withToken($apikey->token)
			->get("https://api.correios.com.br/cep/v2/enderecos/$postalCode");

		return [
			"postal_code" => $response["cep"]?? null,
			"adress" => $response["logradouro"] ?? null,
			"county" => $response["bairro"]?? null,
			"city" => $response["localidade"] ?? null,
			"uf"=> $response["uf"] ?? null,
		];
	}

    private function fetchShipping(string $apiToken, string $originZipCode, string $destinyZipCode)
	{
		$today = date('d-m-Y');

		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->withToken($apiToken)
			->post("https://api.correios.com.br/prazo/v1/nacional", [
				'idLote' => '1',
				'parametrosPrazo' => array_map(fn($serviceCode, $index) => [
					'coProduto' => $serviceCode,
					'cepOrigem' => $originZipCode,
					'cepDestino' => $destinyZipCode,
					'dataPostagem' => $today,
					'nuRequisicao' => "$index"
				], $this->correiosServiceCodes, array_keys($this->correiosServiceCodes))
			]);
		
		return $response;
	}

    private function fetchPrice(string $apiToken, string $originZipCode, string $destinyZipCode, string|null $weight)
	{
		$today = date('d-m-Y');
		$weight = ($weight ?? 1) * 1000;

		$response = Http::withHeaders(["X-locale" => "pt_BR"])
			->withToken($apiToken)
			->post("https://api.correios.com.br/preco/v1/nacional/", [
				'idLote' => 'string',
				'parametrosProduto' => array_map(fn($serviceCode) => [
					'coProduto' => $serviceCode,
					'nuRequisicao'=> '1',
					'nuContrato' => '9912449300',
					'nuDR' => 72,
					'cepOrigem' => $originZipCode,
					'psObjeto' => $weight,
					'tpObjeto' => '2',
					'comprimento' => '25',
					'largura' => '35',
					'altura' => '3',
					'dtEvento' => $today,
					'cepDestino' => $destinyZipCode,
				], $this->correiosServiceCodes, array_keys($this->correiosServiceCodes))
			]);

		return $response;
	}

	private function generateToken()
	{
		$response = Http::withBasicAuth(env('CORREIOS_USERNAME'), env('CORREIOS_PASSWORD'))
			->post('https://api.correios.com.br/token/v1/autentica/cartaopostagem', [
				'numero' => env('CORREIOS_CARTAO_POSTAGEM')
			]);

		$this->writeApiCredentialDB('correios', $response);
	}    

	private array $correiosServiceCodes = ['04227', '03298', '03220', '03204'];

}