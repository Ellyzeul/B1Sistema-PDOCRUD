<?php namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\AskRating;
use App\Models\PDOCrudWrapper;

class Order
{
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

        $blingResponse = Order::getBlingRequest($apiKey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        [ $clientName, $clientEmail, $orderNumber, $bookName ] = $blingResponse;

        Mail::to("daniel.monteiro@biblio1.com.br")
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

        $blingResponse = Order::getBlingRequest($apiKey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        [ $clientName, $clientEmail, $orderNumber, $bookName, $phone] = $blingResponse;

        return [
            [
                "formatted_message" => view('whatsapp/ask-rating/national', [
                    'orderNumber' => $orderNumber,
                    'clientName' => $clientName,
                    'bookName' => $bookName,
                    'companyName' => $companyName,
                ])->render(),
                "cellphone" => $phone
            ], 
            200
        ];
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

    private function getBlingRequest(string $apiKey, string $blingNumber)
    {
        $response = Http::get("https://bling.com.br/Api/v2/pedido/$blingNumber/json?apikey=$apiKey");
        if(!$response->ok()) return ["error" => [
            ["message" => "Erro na requisição de dados no Bling. Tente novamente mais tarde..."], 
            500
        ]];

        $order = $response['retorno']['pedidos'][0]['pedido'];

        return [
            $order['cliente']['nome'],
            $order['cliente']['email'],
            $order['numeroPedidoLoja'],
            $order['itens'][0]['item']['descricao'],
            $order['cliente']['celular'],
        ];
    }

    private function updateReadyTo6_2()
    {
        DB::table('order_control')
            ->where('id_phase', '6.1')
            ->update(['ready_to_6_2' => DB::raw('IF(DATEDIFF(NOW(), delivered_date) < 5, "Não", "Sim")')]);
    }
}
