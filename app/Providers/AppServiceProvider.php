<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $blingAPIKeys = [
            0 => 'SELINE_BLING_API_TOKEN',
            1 => 'B1_BLING_API_TOKEN',
        ];

        Http::macro('bling', function (int $companyId, string $version) use ($blingAPIKeys) {
            if($version === 'v2') return Http::withUrlParameters([
                'apikey' => env($blingAPIKeys[$companyId])
            ])->baseUrl('https://bling.com.br/Api/v2');
            
            if($version === 'v3') return Http::withHeaders([
                'x-api-key' => env($blingAPIKeys[$companyId])
            ])->baseUrl('https://bling.com.br/Api/v3');
        });

        Http::macro('mercadoLivre', function(
            bool $authless = false, 
            string | null $accessToken = null, 
            array | null $authForm = null
        ) 
        {
            $baseUrl = 'https://api.mercadolibre.com';

            if($authless) return Http::baseUrl($baseUrl);
            if(isset($accessToken)) return Http::withToken($accessToken)->baseUrl($baseUrl);
            if(isset($authForm)) return Http::asForm()->post("$baseUrl/oauth/token", $authForm)->json();
        });

        Http::macro('enviaDotCom', fn(string $token) => 
            Http::withToken($token)->baseUrl('https://queries.envia.com')
        );

        Http::macro('nuvemshop', function (
            string $token, 
            string $shopId, 
            string $appName, 
            string $appDomain
        )
        {
            return Http::withHeaders([
                'User-Agent' => "$appName ($appDomain)",
                'Authentication' => "bearer $token"
            ])->baseUrl("https://api.nuvemshop.com.br/v1/$shopId");
        });

        Http::macro('b1servicos', function() {
            return Http::baseUrl('https://servicos.b1sistema.com.br/api');
        });
    }
}
