<?php namespace App\Actions\Tracking\DeliveryMethods;

use Illuminate\Support\Facades\Http;

class EnviaDotcom
{
    public function fetch(string $tracking_code)
    {
        $response = Http::withToken(env('ENVIA_DOT_COM_API_KEY'))
            ->get("http://queries.envia.com/guide/$tracking_code");

        // $response = request('GET', 'http://queries.envia.com/guide/$tracking_code', [
        //     'headers' => [
        //         'Accept' => 'application/json',
        //         'Authorization' => 'Bearer '.env('ENVIA_DOT_COM_API_KEY'),
        //     ],
        // ]);

        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        //     'Authorization' => 'Bearer '. env('ENVIA_DOT_COM_API_KEY'),
        // ])->get("http://queries.envia.com/guide/$tracking_code");

        return [$tracking_code => $response->object()];
    }
}