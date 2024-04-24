<?php namespace App\Services\ThirdParty;

use App\Models\ApiCredential;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bling
{
    private int $companyId;
    private string $version;
    private string $previousOrderNumber;
    private string $orderId;
    private string $contactId;

    const API_KEYS = [ 0 => 'bling_seline', 1 => 'bling_b1' ];
    const OAUTH_CREDENTIALS = [
        0 => ['id' => 'SELINE_BLING_CLIENT_ID', 'secret' => 'SELINE_BLING_CLIENT_SECRET'],
        1 => ['id' => 'B1_BLING_CLIENT_ID', 'secret' => 'B1_BLING_CLIENT_SECRET'],
    ];

    public function __construct(int $companyId, string $version = 'v3')
    {
        if($version !== 'v2' && $version !== 'v3') {
            throw new \Exception("Bling API version: $version. is not supported");
        }

        $this->companyId = $companyId;
        $this->version = $version;
    }

    public function getContactFromOrder(string $blingNumber)
    {
        return $this->version === 'v3'
            ? $this->getContactFromOrderV3($blingNumber)
            : $this->getContactFromOrderV2($blingNumber);
    }

    private function getContactFromOrderV2(string $blingNumber)
    {
        throw new \Exception('Unimplemented');
    }

    private function getContactFromOrderV3(string $blingNumber)
    {
        if(isset($this->previousOrderNumber) ||$blingNumber !== $this->previousOrderNumber || !isset($this->contactId)) {
            $this->contactId = $this->getContactIdFromOrderV3($blingNumber);
        }

        $response = Http::bling($this->companyId, 'v3')->get("/contatos/{$this->contactId}");
        if(!$response->ok()) return (object) ['error' => true];

        return $response->object()->data;
    }
    private function getContactIdFromOrderV3(string $blingNumber)
    {
        return $this->getOrderV3($blingNumber)->contato->id;
    }

    /**
     * Atualiza um contato por completo
     * 
     * Endpoint PUT /contatos
     * 
     * @param (string) contactId
     */

    public function putContact(string $contactId, array $requestBody)
    {
        return $this->version === 'v3'
            ? $this->putContactV3($contactId, $requestBody)
            : new \Exception('Unimplemented');
    }

    private function putContactV3(string $contactId, array $requestBody)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->put("/contatos/$contactId", $requestBody);

        return $response->ok()
            ? $response->object()->data
            : $response->object();
    }

        /**
     * Busca contato 
     * 
     * Endpoint GET /contatos
     * 
     * @param (string) contactId
     */

     public function getContact(array $params)
     {
         return $this->version === 'v3'
             ? $this->getContactV3($params)
             : new \Exception('Unimplemented');
     }
 
     private function getContactV3(array $params)
     {
        $accessToken = $this->auth();
        $urlParams = http_build_query($params);
        $response = Http::bling($this->companyId, 'v3', $accessToken)->get("/contatos?$urlParams");

        return $response->ok()
            ? (
                count($response->object()->data) === 1
                    ? $response->object()->data[0]
                    : null
            )
            : $response->object();
     }

    /**
     * Grava um novo contato.
     * 
     * Endpoint POST /contatos
     * 
     * @param (array) requestBody
     */    
    public function postContact(array $requestBody)
    {
        return $this->version === 'v3'
            ? $this->postContactV3($requestBody)
            : new \Exception('Unimplemented');
    }

    private function postContactV3(array $requestBody)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->post("/contatos", $requestBody);

        return $response->getStatusCode() === 201
            ? $response->object()->data
            : $response->object();
    } 
    /**
     * Recupera um produto
     * 
     * Endpoint GET /produtos
     * 
     * @param (string) productId
     */

    public function getProduct(string $productId)
    {
        return $this->version === 'v3'
            ? $this->getProductV3($productId)
            : new \Exception('Unimplemented');
    }

    private function getProductV3(string $productId)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->get("/produtos/$productId");
        if(!$response->ok()) return (object) ['error' => true];

        return $response->object()->data;
    }

    /**
     * Busca produto pelo código
     * 
     * Endpoint GET /produtos?codigo
     * 
     * @param (string) code
     */

    public function getProductByCode(string $code)
    {
        return $this->version === 'v3'
            ? $this->getProductByCodeV3($code)
            : new \Exception('Unimplemented');
    }

    private function getProductByCodeV3(string $code)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->get("/produtos?codigo=$code");
        if(!$response->ok() || count($response->object()->data) == 0) return (object) ['error' => true];

        return $this->getProductV3($response->object()->data[0]->id);
    }

    /**
     * Atualiza um produto por completo
     * 
     * Endpoint PUT /produtos
     * 
     * @param (string) productId
     */

    public function putProduct(string $productId, array $requestBody)
    {
        return $this->version === 'v3'
            ? $this->putProductV3($productId, $requestBody)
            : new \Exception('Unimplemented');
    }

    private function putProductV3(string $productId, array $requestBody)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->put("/produtos/$productId", $requestBody);

        return $response->ok()
            ? $response->object()->data
            : $response->object();
    }

    /**
     * Grava o pedido.
     * 
     * Endpoint POST /produtos
     * 
     * @param (array) requestBody
     */    
    public function postProduct(array $requestBody)
    {
        return $this->version === 'v3'
            ? $this->postProductV3($requestBody)
            : new \Exception('Unimplemented');
    }

    private function postProductV3(array $requestBody)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->post("/produtos", $requestBody);

        return $response->ok()
            ? $response->object()->data
            : $response->object();
    }    

    /**
     * Recupera dados do pedido.
     * 
     * Endpoint GET /vendas
     * 
     * @param (string) blingNumber
     */
    public function getOrder(string $blingNumber)
    {
        return $this->version === 'v3'
            ? $this->getOrderV3($blingNumber)
            : $this->getOrderV2($blingNumber);
    }

    private function getOrderV2(string $blingNumber)
    {
        $response = Http::bling($this->companyId, 'v2')->get("/pedido/$blingNumber/json");
        if(!$response->ok()) return (object) ['error' => true];

        return $response
            ->object()
            ->pedidos[0]
            ->pedido;
    }

    private function getOrderV3(string $blingNumber)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->get("/pedidos/vendas?numero=$blingNumber");
        Log::debug(json_encode($response->object()));
        $data = $response->object()->data[0];

        $this->previousOrderNumber = $blingNumber;
        
        return $response->ok()
            ? $this->getOrderById($data->id)
            : (object) ['error' => true];
    }

    /**
     * Recupera dados do pedido.
     * 
     * Endpoint GET /vendas
     * 
     * @param (string) OrderId (bling)
     */

    public function getOrderById(string $orderId)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->get("/pedidos/vendas/{$orderId}");
        $data = $response->object()->data;
        
        $this->contactId = $data->contato->id;
        
        return $response->ok()
            ? $data
            : (object) ['error' => true];
    }    
        /**
     * Cria um pedido de venda
     * 
     * Endpoint POST /pedido/vendas/
     */

     public function postOrder(array $requestBody)
     {
         return $this->version === 'v3'
             ? $this->postOrderV3($requestBody)
             : throw new \Exception("Unimplemented");
     }
 
     public function postOrderV3(array $requestBody)
     {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->post("/pedidos/vendas", $requestBody);
         
        return $response->ok()
            ? $response->object()->data
            : $response->object();
     }

    /**
     * Atualiza pedido de venda
     * 
     * Endpoint PUT /vendas/{id}
     */

    public function putOrder(string $orderId, array | object $requestBody)
    {
        return $this->version === 'v3'
            ? $this->putOrderV3($orderId, $requestBody)
            : throw new \Exception("Unimplemented");
    }

    public function putOrderV3(string $orderId, array | object $requestBody)
    {
        $accessToken = $this->auth();
        $response = Http::bling($this->companyId, 'v3', $accessToken)->put("/pedidos/vendas/{$orderId}", $requestBody);
        
        return $response->ok()
            ? $response->object()->data
            : $response->object();
    }

    /**
     * Funções auxiliares do endpoint /vendas
     */

    private function setOrderId(string $blingNumber, string $accessToken)
    {
        if(isset($this->previousOrderNumber) && $blingNumber === $this->previousOrderNumber) return (object) ['error' => false];

        $response = Http::bling($this->companyId, 'v3', $accessToken)->get("/pedidos/vendas?numero=$blingNumber");
        if(!$response->ok()) return (object) ['error' => true];

        $this->orderId = $response->object()->data[0]->id;

        return (object) ['error' => false];
    }

    private function auth(bool $force=false)
    {
        $credential = ApiCredential::get(self::API_KEYS[$this->getBlingId()] ?? 1);

        if($this->isCredentialExpired($credential) || $force) {
            $credential = $this->fetchUpdatedCredential($credential);
        }

        return $credential->access_token;
    }

    private function isCredentialExpired(object $credential)
    {
        return Date::now()->diffInMilliseconds($credential->expire_date, false) <= 0;
    }

    private function fetchUpdatedCredential(object $credential): object
    {
        $now = Date::now();
        $oauthCredential = $this->getOAuthCredentials();
        $updated = Http::bling($this->companyId, 'v3')->withBasicAuth(
            $oauthCredential->id,
            $oauthCredential->secret,
        )->post('/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $credential->refresh_token,
        ])->object();

        $updated->expire_date = $now->addSeconds($updated->expires_in)->toISOString();

        ApiCredential::set(self::API_KEYS[$this->getBlingId()], $updated);

        return $updated;
    }

    private function getOAuthCredentials(): object
    {
        $id = $this->getBlingId();
        $envKeys = self::OAUTH_CREDENTIALS[$id];

        return (object) [
            'id' => env($envKeys['id']),
            'secret' => env($envKeys['secret']),
        ];
    }

    private function getBlingId(): int
    {
        return $this->companyId <= 1 ? $this->companyId : 1;
    }
}
