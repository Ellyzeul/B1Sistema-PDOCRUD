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
            ])->baseUrl('https://bling.com.br/a/Api/v3');
        });
    }
}
