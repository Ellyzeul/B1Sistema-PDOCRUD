<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\AskRating;
use App\Models\PDOCrudWrapper;
use PhpParser\Node\Expr\Cast\String_;

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

    public function read(string|null $phase)
    {
        if($phase === "6.1") $this->updateReadyTo6_2();

        $pdocrud = new PDOCrudWrapper();
        $crud = $pdocrud->getHTML($phase);

        return $crud;
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

    public function sendAskRatingEmail(string $orderId)
    {
        [ $blingNumber, $apiKey, $fromEmail, $companyName, $isNational ] = Order::getMailingInfo($orderId);

        $blingResponse = Order::getBlingMessagingInfo($apiKey, $blingNumber);
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
        [ $blingNumber, $apiKey, $fromEmail, $companyName, $isNational ] = Order::getMailingInfo($orderId);

        $blingResponse = Order::getBlingMessagingInfo($apiKey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        [ $clientName, $clientEmail, $orderNumber, $bookName, $phone] = $blingResponse;

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
        return DB::table('order_addresses')
            ->where('online_order_number', $orderNumber)
            ->first();
    }

    public static function getColumnsNames()
    {
        $pdocrud = new PDOCrudWrapper();

        return $pdocrud->getColumnsNames();
    }

    public function updateTrackingCode(string $orderId, string $blingNumber)
    {
        $apiKey = env('SELINE_BLING_API_TOKEN');

        $blingResponse = Order::getBlingTrakingCodeRequest($apiKey, $blingNumber);
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

    public function updateDeliveryMethod(string $orderId, string $blingNumber)
    {
        $apiKey = env('SELINE_BLING_API_TOKEN');

        $blingResponse = Order::getBlingDeliveryMethodRequest($apiKey, $blingNumber);
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

    private function getBlingMessagingInfo(string $apiKey, string $blingNumber)
    {
        $response = $this->makeBlingRequest($apiKey, $blingNumber);
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

    private function getBlingTrakingCodeRequest(string $apiKey, string $blingNumber)
    {
        $response = $this->makeBlingRequest($apiKey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        return $order['transporte']['volumes'][0]['volume']['codigoRastreamento'];        
    }

    private function getBlingDeliveryMethodRequest(string $apiKey, string $blingNumber)
    {
        $response = $this->makeBlingRequest($apiKey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        return $order['transporte']['volumes'][0]['volume']['servico'];        
    }

    private function makeBlingRequest(string $apiKey, string $blingNumber)
    {
        $response = Http::get("https://bling.com.br/Api/v2/pedido/$blingNumber/json?apikey=$apiKey");
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
