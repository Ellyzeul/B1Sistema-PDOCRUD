<?php namespace App\Actions\OrderMessage\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

trait AskRatingMessageCommon
{
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

    private function makeBlingOrderRequest(string $apikey, string $blingNumber)
    {
        $response = Http::get("https://bling.com.br/Api/v2/pedido/$blingNumber/json?apikey=$apikey");
        if(!$response->ok()) return ["error" => [
            ["message" => "Erro na requisição de dados no Bling. Tente novamente mais tarde..."], 
            500
        ]];

        return $response;
    }    

    private function sendEmail(string $email, $content)
    {
        Mail::to($email)
            ->send($content);
    }
}
