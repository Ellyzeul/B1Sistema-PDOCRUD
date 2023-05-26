<?php

namespace App\Models\Services;

use Illuminate\Support\Facades\Http;

class Bling
{
    private int $companyId;
    private string $version;
    private string $previousOrderNumber;
    private string $orderId;
    private string $contactId;

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
        $response = Http::bling($this->companyId, 'v3')->put("/contatos/$contactId", $requestBody);

        return $response->ok()
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
        $response = Http::bling($this->companyId, 'v3')->get("/produtos/$productId");
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
        $response = Http::bling($this->companyId, 'v3')->get("/produtos?codigo=$code");
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
        $response = Http::bling($this->companyId, 'v3')->put("/produtos/$productId", $requestBody);

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
        $response = Http::bling($this->companyId, 'v3')->post("/produtos", $requestBody);

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
        if($this->setOrderId($blingNumber)->error) return (object) ['error' => true];

        $response = Http::bling($this->companyId, 'v3')->get("/pedidos/vendas/{$this->orderId}");
        $data = $response->object()->data;

        $this->previousOrderNumber = $blingNumber;
        $this->contactId = $data->contato->id;
        
        return $response->ok()
            ? $data
            : (object) ['error' => true];
    }

    /**
     * Atualiza pedido de venda
     * 
     * Endpoint PUT /vendas/{id}
     */

    public function putOrder(string $orderId, array $requestBody)
    {
        return $this->version === 'v3'
            ? $this->putOrderV3($orderId, $requestBody)
            : throw new \Exception("Unimplemented");
    }

    public function putOrderV3(string $orderId, array $requestBody)
    {
        $response = Http::bling($this->companyId, 'v3')->put("/pedidos/vendas/{$orderId}", $requestBody);
        
        return $response->ok()
            ? $response->object()->data
            : $response->object();
    }

    /**
     * Funções auxiliares do endpoint /vendas
     */

    private function setOrderId(string $blingNumber)
    {
        if(isset($this->previousOrderNumber) && $blingNumber === $this->previousOrderNumber) return (object) ['error' => false];

        $response = Http::bling($this->companyId, 'v3')->get("/pedidos/vendas?numero=$blingNumber");
        if(!$response->ok()) return (object) ['error' => true];

        $this->orderId = $response->object()->data[0]->id;

        return (object) ['error' => false];
    }
}
