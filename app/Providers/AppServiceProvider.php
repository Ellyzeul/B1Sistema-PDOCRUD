<?php namespace App\Providers;

use Illuminate\Http\Client\PendingRequest;
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
        $blingApiKey = function(int $idCompany): string {
            return [ 0 => 'SELINE_BLING_API_TOKEN', 1 => 'B1_BLING_API_TOKEN', ][$idCompany]
                ?? 'B1_BLING_API_TOKEN';
        };

        Http::macro('bling', function (int $companyId, string $version, ?string $accessToken=null) use ($blingApiKey) {
            if($version === 'v2') return Http::withUrlParameters([
                'apikey' => env($blingApiKey($companyId))
            ])->baseUrl('https://bling.com.br/Api/v2');
            
            if($version === 'v3') return isset($accessToken)
                ? Http::withToken($accessToken)->baseUrl('https://bling.com.br/Api/v3')
                : Http::baseUrl('https://bling.com.br/Api/v3');
        });

        $mercadoLivreHandler = function(
            ?bool $authless = false, 
            ?string $accessToken = null, 
            ?array $authForm = null
        ) 
        {
            $baseUrl = 'https://api.mercadolibre.com';

            if($authless) return Http::baseUrl($baseUrl);
            if(isset($accessToken)) return Http::withToken($accessToken)->baseUrl($baseUrl);
            if(isset($authForm)) return Http::asForm()->post("$baseUrl/oauth/token", $authForm)->json();
        };
        Http::macro('mercadoLivre', $mercadoLivreHandler);
        PendingRequest::macro('mercadoLivre', $mercadoLivreHandler);

        Http::macro('enviaDotCom', fn(string $token, string $scope) => 
            Http::withToken($token)->baseUrl("https://$scope.envia.com")
        );

        Http::macro('envia', fn() => 
            Http::withToken(env('ENVIA_DOT_COM_API_TOKEN'))->baseUrl('https://api.envia.com')
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
            return Http::baseUrl(env('B1SERVICOSURL'));
        });

        Http::macro('kangu', function(string $company) {
            $tokens = [
                'seline' => env('KANGU_TOKEN_SELINE'),
                'b1' => env('KANGU_TOKEN_B1'),
            ];

            return Http::baseUrl('https://portal.kangu.com.br/tms/transporte')->withHeaders([
                'Token' => $tokens[$company]
            ]);
        });

        Http::macro('usps', function(string $resource, ?string $accessToken = null){
            $configClient = Http::baseUrl("https://api.usps.com/$resource/v3/");

            if($resource === 'oauth2') return $configClient;
            if(!isset($accessToken)) throw new \Exception("Access token is mandatory on USPS API when requesting $resource resource!");

            return $configClient->withToken($accessToken);
        });

        Http::macro('b1rastreamento', function() {
            return Http::baseUrl(config('cache.intregrations.b1rastreamento.url') . '/api')
                ->withToken(config('cache.intregrations.b1rastreamento.token'));
        });

        Http::macro('delnext', function(string $country = 'PT', string | null $apikey = null) {
            if(!isset($apikey)) return Http::baseUrl("https://www.delnext.com/$country");

            return Http::baseUrl("https://www.delnext.com/$country");
        });

        Http::macro('loggi', function(?string $token = null, bool $auth = false) {
            $baseUrl = 'https://api.loggi.com';
            if($auth) return Http::baseUrl($baseUrl);

            $client = Http::baseUrl("$baseUrl/v1/companies/". env('LOGGI_ID_COMPANY'));

            return isset($token)
                ? $client->withToken($token)
                : $client;
        });

        Http::macro('focusNfe', function(string $company, string $version = 'v2', bool $debug = false) {
            if($debug) {
                $token = env($company === 'seline' ? 'FOCUS_NFE_SELINE_TOKEN_TEST' : 'FOCUS_NFE_B1_TOKEN_TEST');
                return Http::baseUrl("https://homologacao.focusnfe.com.br/$version")->withBasicAuth($token, '');
            }

            $token = env($company === 'seline' ? 'FOCUS_NFE_SELINE_TOKEN' : 'FOCUS_NFE_B1_TOKEN');
            return Http::baseUrl("https://api.focusnfe.com.br/$version")->withBasicAuth($token, '');
        });
    }
}
