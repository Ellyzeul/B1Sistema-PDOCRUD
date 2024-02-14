<?php namespace App\Services\ThirdParty;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * Recupera item por ID
     */

    public function getItemById(string $id, int $attempt = 0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(authless: true)->get("/items/$id");

        return $response->object();
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

    public function getShipmentByOrderId(string $orderId, int $attempt=0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])->get("/orders/$orderId/shipments");

        if($response->unauthorized()) return $this->authenticate(true, fn() => $this->getShipment(
            $orderId, 
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

    public function getMessages(array $orderNumbers, int $attempt=0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $responses = Http::pool(fn(Pool $pool) => array_map(fn($orderNumber) => 
            $pool->as($orderNumber)
                ->withToken($this->credential['access_token'])
                ->get("https://api.mercadolibre.com/messages/packs/$orderNumber/sellers/$this->sellerId?tag=post_sale"), 
            $orderNumbers
        ));
        Log::debug(json_encode(array_map(fn($resp) => $resp->getStatusCode(), $responses), JSON_PRETTY_PRINT));

        $messages = [];

        foreach($orderNumbers as $orderNumber) {
            $response = $responses[$orderNumber];
            $messages[$orderNumber] = $response->ok()
                ? $response->object()->messages
                : [];
        }

        return $messages;
    }

    public function postMessage(string $resourceId, string $clientId, string $text, int $attempt=0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])->post("/messages$resourceId", [
            'from' => [ 'user_id' => $this->sellerId ], 
            'to' => [ 'user_id' => $clientId ], 
            'text' => $text
        ]);

        return [
            'success' => $response->getStatusCode() === 201
        ];
    }

    /**
     * Recupera perguntas de anúncios
     */

    public function getQuestions(int $attempt=0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();
        $keepFetching = true;
        $offset = 0;
        $questions = [];

        while($keepFetching) {
            $response = $this->handleGetQuestionsIteration($offset);
            $questions = array_merge($questions, $response['questions']);

            $offset += 200;
            if($offset >= $response['total']) $keepFetching = false;
        }

        return $questions;
    }

    private function handleGetQuestionsIteration(int $offset)
    {
        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])
                ->get("/questions/search?seller_id={$this->sellerId}&api_version=4&limit=200&offset=$offset");
            
        if(!$response->ok()) return [ "questions" => [], "total" => 0 ];
        $data = $response->object();

        return [ "questions" => $data->questions, "total" => $data->total ];
    }

    /**
     * Responder mensagens de anúncios
     */

    public function postAnswer(string $questionId, string $text, int $attempt=0)
    {
        if($attempt >= $this->maxAttempts) $this->throwMaxAttemptsError(__FUNCTION__);
        $this->authenticate();

        $response = Http::mercadoLivre(accessToken: $this->credential['access_token'])->post('/answers', [
            'question_id' => $questionId, 
            'text' => $text, 
        ]);

        return [
            'success' => $response->getStatusCode() === 200
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
