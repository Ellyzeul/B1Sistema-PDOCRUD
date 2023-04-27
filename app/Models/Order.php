<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\AskRating;
use App\Models\PDOCrudWrapper;

class Order
{
    private array $deliveryMethods = [
        "PAC CONTRATO AG" => 2,
        "SEDEX CONTRATO AG" => 2,
        "SEDEX HOJE CONTRATO AG" => 2,
        "CORREIOS MINI ENVIOS CTR AG" => 2,
        "SEDEX 12 CONTRATO AG" => 2,
        "SEDEX 10 CONTRATO AG" => 2,
        "SEDEX CONTR GRAND FORMATO" => 2,
        "PAC CONTR GRAND FORMATO" => 2,
        "CARTA SIMPLES SELO E SE PCTE" => 2,
        "CARTA SIMPLES CHANCELA PCTE" => 2,
        "CARTA RG O4 CHANC ETIQUETA" => 2,
        "CARTA REG O4 MFD" => 2,
        "CARTA RG AR CONV O4 CHAN ETIQ" => 2,
        "CARTA REG AR CONV O4 MFD" => 2,
        "CARTA RG AR ELTR O4 CHANC ETIQ" => 2,
        "SEDEX HOJE EMPRESARIAL" => 2,
        "CARTA REG AR ELET O4 MFD" => 2,
        "TRANSFER LOG" =>2,
        ".Package" => 5,
        "Expresso" => 5,
        "Rodoviário" => 5,
        "Econômico" => 5,
        "DOC" => 5,
        "Corporate" => 5,
        ".Com" => 5,
        "Internacional" => 5,
        "Cargo" => 5,
        "Emergencial" => 5,
        "Pickup" => 5
    ];

    private array $blingAPIKeys = [
        0 => 'SELINE_BLING_API_TOKEN',
        1 => 'B1_BLING_API_TOKEN',
    ];

    public function read(string|null $phase)
    {
        if($phase === "6.1") $this->updateReadyTo6_2();

        $pdocrud = new PDOCrudWrapper();
        $crud = $pdocrud->getHTML($phase);

        return $crud;
    }

    public function getShipmentLabelData(string $orderId)
    {
        $order = DB::table('order_control')
            ->select('id_company', 'bling_number', 'id_delivery_method')
            ->where('id', $orderId)
            ->first();
        $company = DB::table('companies')
            ->select('id', 'name', 'company_name', 'cnpj', 'state_registration', 'municipal_registration')
            ->where('id', $order->id_company)
            ->first();
        $apikey = env(($this->blingAPIKeys[$order->id_company]));
        $blingResponse = $this->makeBlingOrderRequest($apikey, $order->bling_number);
        if(isset($blingResponse['error'])) return $blingResponse;

        $blingOrder = $blingResponse['retorno']['pedidos'][0]['pedido'];

        return [
            'company' => $company, 
            'id_delivery_method' => $order->id_delivery_method, 
            'bling_data' => $blingOrder
        ];
    }

    public function updateAddressVerified(array $toUpdate)
    {
        $updateQuery = "INSERT IGNORE INTO order_control (id, address_verified) VALUES ";
        $values = [];
        foreach($toUpdate as $pair) {
            $updateQuery .= "(?, ?),";
            array_push($values, $pair['id'], $pair['address_verified']);
        }
        $updateQuery = substr_replace($updateQuery, " ", -1);
        $updateQuery .= "
            ON DUPLICATE KEY UPDATE
                address_verified = VALUES(address_verified)
        ";

        DB::insert($updateQuery, $values);
        return [
            "message" => "Verificação de endereços atualizada com sucesso."
        ];
    }

    public function updateReadForShip(array $toUpdate)
    {
        foreach($toUpdate as $registry) {
            DB::table('order_control')
                ->where('id', $registry['id'])
                ->update(['ready_for_ship' => $registry['ready_for_ship']]);
        }

        return [
            "message" => "Verificação de endereços atualizada com sucesso."
        ];
    }

    public function getTotalOrdersInPhase()
    {
        $results = DB::table('phases')
            ->select(
                'phases.id', 
                DB::raw('COUNT(order_control.id) as total'), 
                'phases.color'
            )
            ->leftJoin('order_control', 'phases.id', '=', 'order_control.id_phase')
            ->where('phases.id', '<', '7')
            ->orWhere('phases.id', '8.1')
            ->groupBy('phases.id')
            ->get();
        
        return $results;
    }

    public function getDataForAskRatingSpreadSheet()
    {
        $results = DB::table('order_control')
            ->select(
                'id', 
                'online_order_number', 
                'bling_number', 
                'id_company', 
                DB::raw('(SELECT name FROM companies WHERE id = id_company) as company'), 
                DB::raw('(SELECT name FROM sellercentrals WHERE id = id_sellercentral) as sellercentral'), 
                'id_phase'
            )
            ->where('id_phase', '6.2')
            ->orWhere('id_phase', '6.21')
            ->orderBy('id_phase')
            ->skip(0)
            ->take(50)
            ->get();
        
        $handled = array_map(function($result) {
            $blingResponse = Order::getBlingMessagingInfo(
                env($this->blingAPIKeys[$result->id_company]),
                $result->bling_number
            );
            if(isset($blingResponse['error'])) return [
                'id' => $result->id,
                'online_order_number' => $result->online_order_number,
                'company' => $result->company,
                'sellercentral' => $result->sellercentral,
                'id_phase' => $result->id_phase,
                'error' => true,
            ];
            [ $clientName, $clientEmail, $_, $bookName, $_ ] = $blingResponse;

            return [
                'id' => $result->id,
                'online_order_number' => $result->online_order_number,
                'company' => $result->company,
                'sellercentral' => $result->sellercentral,
                'url' => 'https://www.amazon.com.br/hz/feedback/?_encoding=UTF8&orderID=' . $result->online_order_number,
                'email' => $clientEmail,
                'client_name' => $clientName,
                'book_name' => $bookName,
                'id_phase' => $result->id_phase,
                'error' => false
            ];
        }, $results->toArray());

        return $handled;
    }

    public function sendAskRatingEmail(string $orderId)
    {
        [ $blingNumber, $apikey, $fromEmail, $companyName, $isNational ] = Order::getMailingInfo($orderId);

        $blingResponse = Order::getBlingMessagingInfo($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        [ $clientName, $clientEmail, $orderNumber, $bookName ] = $blingResponse;

        Mail::to($clientEmail)
            ->send(new AskRating(
                $fromEmail, 
                $isNational,
                $clientName, 
                $orderNumber, 
                $bookName, 
                $companyName, 
            ));
        
        DB::table('order_control')
            ->where('id', $orderId)
            ->increment('ask_rating');

        return [["message" => "E-mail enviado com sucesso!"], 200];
    }
   
    public function getAskRatingWhatsapp(string $orderId)
    {
        [ $blingNumber, $apikey, $fromEmail, $companyName, $isNational ] = Order::getMailingInfo($orderId);

        $blingResponse = Order::getBlingMessagingInfo($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        [ $clientName, $clientEmail, $orderNumber, $bookName, $phone ] = $blingResponse;

        return [[
            'formatted_message' => view('whatsapp/ask-rating/national', [
                'orderNumber' => $orderNumber,
                'clientName' => $clientName,
                'bookName' => $bookName,
                'companyName' => $companyName,
            ])->render(),
            'cellphone' => $phone
        ], 200];
    }    

    public function getAddress(string $orderNumber)
    {
        $response = [];
        $blingData = DB::table('order_control')
            ->select('id_company', 'bling_number')
            ->where('online_order_number', $orderNumber)
            ->first();
        $companyId = $blingData->id_company;
        $blingNumber = $blingData->bling_number;
        $apikey = env($this->blingAPIKeys[$companyId]);
        $blingAddress = isset($blingNumber)
            ? $this->getBlingAddress($apikey, $blingNumber)
            : ['error' => 'Sem número do Bling'];

        $response['sellercentral'] = DB::table('order_addresses')
            ->where('online_order_number', $orderNumber)
            ->first();
        $response['sellercentral']->id_sellercentral = DB::table('order_control')
            ->select('id_sellercentral')
            ->where('online_order_number', $orderNumber)
            ->first()
            ->id_sellercentral;

        $response['bling'] = isset($blingAddress['error'])
            ? null
            : array_merge($blingAddress, ['bling_number' => $blingNumber]);
        
        return $response;
    }

    public static function getColumnsNames()
    {
        $pdocrud = new PDOCrudWrapper();

        return $pdocrud->getColumnsNames();
    }

    public function updateTrackingCode(string $companyId, string $orderId, string $blingNumber)
    {
        $apikey = env($this->blingAPIKeys[$companyId]);
        $blingResponse = Order::getBlingTrakingCodeRequest($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        $trackingCode = $blingResponse;

        DB::table('order_control')
            ->where('id', $orderId)
            ->update(['tracking_code' => $trackingCode]);

        return [[
            'message' => 'Código de rastreio atualizado!',
            'tracking_code' => $trackingCode
        ], 200];
    }

    public function updateDeliveryMethod(string $companyId, string $orderId, string $blingNumber)
    {
        $apikey = env($this->blingAPIKeys[$companyId]);

        $blingResponse = Order::getBlingDeliveryMethodRequest($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return ['message' => 'Erro na requisição de dados ao Bling...'];

        $deliveryMethod = $blingResponse;

        $deliveryMethodId = $this->deliveryMethods[$deliveryMethod];

        DB::table('order_control')
            ->where('id', $orderId)
            ->update(['id_delivery_method' => $deliveryMethodId]);

        return [[
            'message' => 'Forma de envio atualizada!',
            'delivery_method' => $deliveryMethodId,
        ], 200];
    }

    public function updateInvoiceNumber(string $orderId, string | null $invoiceNumber)
    {
        $databaseData = DB::table('order_control')
                ->select('id_company', 'bling_number')
                ->where('id', '=', $orderId)
                ->first();
                 
        $companyId = $databaseData->id_company; 
        $blingNumber = $databaseData->bling_number;

        if(isset($invoiceNumber)) return null;

        $data = $this->getInvoiceLink($companyId, $blingNumber);
        $invoice_number = $data['invoice_number'];
        $serie = $data['serie'];
        $link = $data['link'];

        DB::table('order_control')
        ->where('id', $orderId)
        ->update(['invoice_number' => $invoice_number]);

        return [
                "invoice_number" => $invoice_number,
                "serie" => $serie,
                "link" => $link
        ];
    }
    
    public function getInvoiceLink(string $companyId, string $blingNumber)
    {
        $data = $this->getInvoiceNumberAndSerie($companyId, $blingNumber);
        $invoice_number = $data['invoice_number'];
        $serie = $data['serie'];

        $apikey = env($this->blingAPIKeys[$companyId]);
        $response = $this->makeBlingInvoiceRequest($apikey, $invoice_number, $serie);
        if(isset($response['error'])) return $response;

        $link = $response['retorno']['notasfiscais'][0]['notafiscal']['linkDanfe'];

        return [
            "invoice_number" => $invoice_number,
            "serie" => $serie,
            "link" => $link
        ];
    }

    private function getInvoiceNumberAndSerie(string $companyId, string $blingNumber)
    {
        $apikey = env($this->blingAPIKeys[$companyId]);
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        $invoice_number = $order['nota']['numero'] ?? null;
        $serie = $order['nota']['serie'] ?? null;

        return [
            "invoice_number" => $invoice_number,
            "serie" => $serie
        ];
    }

    private function array_some(array $array, callable $fn)
    {
        foreach($array as $value) {
            if($fn($value)) return true;
        }

        return false;
    }

    private function getMailingInfo(string $orderId)
    {
        $result = DB::table('order_control')
            ->join('mailer_info', 'order_control.id_company', '=', 'mailer_info.id_company')
            ->select(
                'order_control.bling_number', 
                'mailer_info.token_name', 
                'mailer_info.from_email', 
                'mailer_info.company_name', 
                DB::raw('(
                    SELECT IF(POSITION("BR" IN name) > 0, 1, 0) 
                    FROM sellercentrals
                    WHERE id = order_control.id_sellercentral
                ) AS is_national')
            )
            ->where('order_control.id', $orderId)
            ->first();
        
        return [
            $result->bling_number, 
            env($result->token_name), 
            $result->from_email, 
            $result->company_name, 
            $result->is_national == 1, 
        ];
    }

    private function getBlingAddress(string $apikey, string $blingNumber)
    {
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];
        $client = $order['cliente'];
        $totalItems = array_reduce(
            array_map(fn ($item) => intval($item['item']['quantidade']), $order['itens']), 
            fn ($acc, $qnt) => $acc + $qnt
        );

        return [
            'buyer_name' => $client['nome'],
            'cpf_cnpj' => $client['cnpj'],
            'ie' => $client['ie'],
            'address' => $client['endereco'],
            'number' => $client['numero'],
            'complement' =>$client['complemento'],
            'city' => $client['cidade'],
            'county' => $client['bairro'],
            'email' => $client['email'],
            'cellphone' => $client['celular'],
            'landline_phone' => $client['fone'],
            'postal_code' => $client['cep'],
            'uf' => $client['uf'],
            'total_items' => $totalItems,
            'total_value' => $order['totalvenda'],
        ];
    }

    private function getBlingMessagingInfo(string $apikey, string $blingNumber)
    {
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        return [
            $order['cliente']['nome'],
            $order['cliente']['email'],
            $order['numeroPedidoLoja'],
            $order['itens'][0]['item']['descricao'],
            $order['cliente']['celular']
        ];
    }

    private function getBlingTrakingCodeRequest(string $apikey, string $blingNumber)
    {
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        $trackingCode = $order['transporte']['volumes'][0]['volume']['codigoRastreamento'];

        if($trackingCode == "") $trackingCode = $order['transporte']['volumes'][0]['volume']['remessa']['numero'];

        return $trackingCode;         
    }

    private function getBlingDeliveryMethodRequest(string $apikey, string $blingNumber)
    {
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        return $order['transporte']['volumes'][0]['volume']['servico'];        
    }

    private function makeBlingOrderRequest(string $apikey, string $blingNumber)
    {
        $response = Http::get("https://bling.com.br/Api/v2/pedido/$blingNumber/json?apikey=$apikey");
        if(!$response->ok()) return ["error" => [
            ["message" => "Erro na requisição de dados no Bling. Tente novamente mais tarde..."], 
            500
        ]];

        return $response;
    }

    private function makeBlingInvoiceRequest(string $apikey, string $invoice_number, string $serie)
    {
        $response = Http::get("https://bling.com.br/Api/v2/notafiscal/$invoice_number/$serie/json/?apikey=$apikey");
        if(!$response->ok()) return ["error" => [
            ["message" => "Erro na requisição de dados no Bling. Tente novamente mais tarde..."], 
            500
        ]];

        return $response;
    }

    private function updateReadyTo6_2()
    {
        DB::table('order_control')
            ->where('id_phase', '6.1')
            ->update(['ready_to_6_2' => DB::raw('IF(DATEDIFF(NOW(), delivered_date) < 5, "Não", "Sim")')]);
    }
}