<?php namespace App\Services\ThirdParty;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MercadoLivre
{
    private array | null $credential;
    private string $clientId;
    private string $clientSecret;
    private string $refreshToken;
    private string $sellerId;
    private string $credentialName;
    private int $maxAttempts = 3;
    private array $authData = [
        0 => [
            'client_id' => 'MERCADO_LIVRE_SELINE_CLIENT_ID', 
            'client_secret' => 'MERCADO_LIVRE_SELINE_CLIENT_SECRET', 
            'refresh_token' => 'MERCADO_LIVRE_SELINE_REFRESH_TOKEN', 
            'seller_id' => 'MERCADO_LIVRE_SELINE_SELLER_ID', 
            'credential_name' => 'mercado-livre-seline', 
        ], 
        1 => [
            'client_id' => 'MERCADO_LIVRE_B1_CLIENT_ID', 
            'client_secret' => 'MERCADO_LIVRE_B1_CLIENT_SECRET', 
            'refresh_token' => 'MERCADO_LIVRE_B1_REFRESH_TOKEN', 
            'seller_id' => 'MERCADO_LIVRE_B1_SELLER_ID', 
            'credential_name' => 'mercado-livre-b1', 
        ], 
    ];

    function __construct(int $idCompany)
    {
        $auth = $this->authData[$idCompany];

        $this->credential = null;
        $this->clientId = env($auth['client_id']);
        $this->clientSecret = env($auth['client_secret']);
        $this->refreshToken = env($auth['refresh_token']);
        $this->sellerId = env($auth['seller_id']);
        $this->credentialName = $auth['credential_name'];
    }

    public function getSellerID(): string
    {
        return $this->sellerId;
    }

    /**
     * Recupera pedido por ID
     */

    public function getOrderById(string $id, int $attempt = 0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])->get("/orders/$id");

        if($response->unauthorized()) return $this->authenticate(true, fn() => $this->getOrderById(
            $id, 
            attempt: $attempt+1, 
        ));

        return $response->object();
    }

    /**
     * Recupera pedidos usando parâmetros de busca
     */

    public function getOrdersBySearch(
        string $dateCreatedFrom = null, 
        string $dateCreatedTo = null, 
        int $attempt = 0, 
    )
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $dateCreatedFrom = isset($dateCreatedFrom)
            ? $dateCreatedFrom
            : $this->credential['order_last_fetch_date'];
        $dateCreatedTo = isset($dateCreatedTo)
            ? $dateCreatedTo
            : date('Y-m-d\TH:i:s', strtotime('now -3 hours'));
        
        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])
            ->get('/orders/search', [
                'seller' => $this->sellerId, 
                'order.date_created.from' => $dateCreatedFrom . '.000-00:00', 
                'order.date_created.to' => $dateCreatedTo . '.000-00:00', 
            ]);
        
        if($response->unauthorized()) return $this->authenticate(true, fn() => $this->getOrdersBySearch(
            $dateCreatedFrom, 
            $dateCreatedTo, 
            attempt: $attempt+1, 
        ));

        if($response->ok()) {
            $this->credential['order_last_fetch_date'] = $dateCreatedTo;
            $this->updateCredential($this->credential);
        }
        
        return $response->object();
    }

    public function getShipment(string $id, int $attempt = 0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])->get("/shipments/$id");

        if($response->unauthorized()) return $this->authenticate(true, fn() => $this->getShipment(
            $id, 
            attempt: $attempt+1, 
        ));

        return $response->object();
    }

    public function getMessage(string $orderNumber, int $attempt=0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])->get(
            "/messages/packs/$orderNumber/sellers/$this->sellerId?tag=post_sale"
        );

        return $response->ok()
            ? $response->object()->messages
            : [];
    }

    public function postMessage(string $resourceId, string $clientId, string $text, int $attempt=0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])->post("/messages$resourceId?tag=post_sale", [
            'from' => [ 'user_id' => $this->sellerId ], 
            'to' => [ 'user_id' => $clientId ], 
            'text' => $text
        ]);

        return [
            'success' => $response->getStatusCode() === 201
        ];
    }

    // Métodos privados

    private function authenticate(bool $refetch = false, callable | null $callback = null)
    {
        if($refetch) {
            $this->fetchCredential(isset($this->credential) ? $this->credential : []);
            return $this->handleAuthCallback($callback);
        }
        if(isset($this->credential)) {
            if($this->needsRefetch($this->credential)) {
                $this->fetchCredential($this->credential);
            }
            return $this->handleAuthCallback($callback);
        }

        $dbReference = DB::table('api_credentials')->where('id', $this->credentialName);

        $credential = $dbReference->exists()
            ? json_decode($dbReference->first()->key, true)
            : null;
        
        if(!isset($credential)) {
            $this->fetchCredential();
            return $this->handleAuthCallback($callback);
        }

        if($this->needsRefetch($credential)) {
            $this->fetchCredential($credential);
            return $this->handleAuthCallback($callback);
        }

        $this->credential = $credential;

        return $this->handleAuthCallback($callback);
    }

    private function handleAuthCallback(callable | null $callback)
    {
        return isset($callback)
            ? $callback()
            : null;
    }

    private function needsRefetch(array $credential)
    {
        $fetchDate = Date::parse($credential['fetch_date']);
        $expireDatetime = $fetchDate->addSeconds($credential['expires_in']);
        $now = Date::parse(date('Y-m-d H:i:s'));

        return $expireDatetime->diffInSeconds($now, false) >= 0;
    }

    private function fetchCredential(array $currentCredential = [])
    {
        $now = date('Y-m-d H:i:s');
        $fetched = Http::mercadoLivre(authForm: [
            'grant_type' => 'refresh_token', 
            'client_id' => $this->clientId, 
            'client_secret' => $this->clientSecret, 
            'refresh_token' => $this->refreshToken, 
        ]);
        $credential = array_merge($currentCredential, $fetched, [
            'fetch_date' => $now, 
        ]);
        if(!isset($credential['order_last_fetch_date'])) {
            $credential['order_last_fetch_date'] = date('Y-m-d\T00:00:00', strtotime('-3 days'));
        }

        DB::table('api_credentials')->upsert([
            'id' => $this->credentialName, 
            'key' => json_encode($credential), 
        ], ['key']);

        $this->credential = $credential;

        return;
    }

    private function updateCredential(array $credential)
    {
        DB::table('api_credentials')->upsert([
            'id' => $this->credentialName, 
            'key' => json_encode($credential), 
        ], ['key']);

        return;
    }

    private function throwMaxAttemptsError(string $methodName)
    {
        throw new \Exception("[MercadoLivre Error]: Maximum attempts reach in $methodName");
    }
}
