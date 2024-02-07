<?php namespace App\Services\ThirdParty;

use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class USPS
{
  private const CREDENTIAL_NAME = 'usps';
  private const MAX_ATTEMPTS = 5;

  private string $accessToken;

  public function __construct()
  {
    $this->accessToken = $this->auth()->access_token;
  }

  public function getTracking(string $trackingCode, string $expand = 'DETAIL')
  {
    $response = Http::usps('tracking', $this->accessToken)->get("/tracking/$trackingCode?expand=$expand");

    return $response->object();
  }

  private function auth()
  {
    $registry = DB::table('api_credentials')
      ->select('key')
      ->where('id', USPS::CREDENTIAL_NAME)
      ->first();

    if(!isset($registry)) return $this->fetchCredential();

    $credential = json_decode($registry->key);
    $diff = Date::parse($credential->request_datetime)->diffInSeconds(Date::now('America/Sao_Paulo'));

    if($diff >= $credential->expires_in) return $this->fetchCredential();
    
    return $credential;
  }

  private function fetchCredential()
  {
    $response = Http::usps('oauth2')
      ->retry(USPS::MAX_ATTEMPTS, 50, throw: false)
      ->post('/token', [
        'grant_type' => 'client_credentials',
        'client_id' => env('USPS_CONSUMER_KEY'),
        'client_secret' => env('USPS_CONSUMER_SECRET'),
      ]);
    if($response->status() !== 200) throw new \Exception("Unable to fetch USPS credentials... Error: " . json_encode($response->object()));
    
    $credential = $response->object();

    $credential->request_datetime = Date::now('America/Sao_Paulo');
    DB::table('api_credentials')->upsert([
      'id' => USPS::CREDENTIAL_NAME,
      'key' => json_encode($credential)
    ], 'id');

    return $credential;
  }
}
