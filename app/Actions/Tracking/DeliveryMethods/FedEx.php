<?php namespace App\Actions\Tracking\DeliveryMethods;

use App\Actions\Tracking\Traits\DeliveryMethodsCommon;
use Illuminate\Support\Facades\Http;

class FedEx
{
    use DeliveryMethodsCommon;

    public function fetch(string $trackingCode)
	{
		if(!$this->existsApiCredentialDB('fedex')) $this->generateToken();

		$apikey = $this->readApiCredentialDB('fedex');

		if(!isset($apikey)){
			$this->generateToken();
			$apikey = $this->readApiCredentialDB('fedex');
		}
		
		try {
			$response = Http::withHeaders(["X-locale" => "pt_BR"])
				->withToken($apikey->access_token)
				->post('https://apis.fedex.com/track/v1/trackingnumbers', [
					"trackingInfo" => [
						[
							"trackingNumberInfo" => [
								"trackingNumber" => $trackingCode
							]
						]
					],
					"includeDetailedScans" => true
				]);
		}
		catch(\Exception $_) {
			return $this->fetch($trackingCode);
		}

		if($response->getStatusCode() == 401) {
			$this->writeApiCredentialDB('fedex', null);
			return $this->fetch($trackingCode);
		}

		$scanEvents = $response['output']['completeTrackResults'][0]['trackResults'][0]['scanEvents'];

		$count = 0;
		$details = "";
		while(sizeof($scanEvents)>=0 && $count<=2){
			if (isset($scanEvents[$count])) {
				$lastUpdateDate = date('Y-m-d', strtotime($scanEvents[$count]['date']));
				$exceptionDescription = $scanEvents[$count]['exceptionDescription'] ?? "";
				$scanLocation = $scanEvents[$count]['scanLocation'];
				$city = $scanLocation['city'] ?? "";
				$stateOrProvinceCode = $scanLocation['stateOrProvinceCode'] ?? "";
				$postalCode = $scanLocation['postalCode'] ?? "";
				$countryName = $scanLocation['countryName'] ?? "";

				$details = $details . "$lastUpdateDate $city $stateOrProvinceCode $postalCode $countryName $exceptionDescription\n";
			}
			$count++;
		}
   
		if(isset($response['output']['completeTrackResults'][0]['trackResults'][0]['serviceCommitMessage'])){
			$msg = $response['output']['completeTrackResults'][0]['trackResults'][0]['serviceCommitMessage']['message'];
			preg_match("/[0-9]{1,2}\sde\s([A-Za-zç]*)\sde\s[0-9]{4}/", $msg, $matches);
			
			if($matches) $deadline = date('Y-m-d', strtotime("+5 weekdays", strtotime($this->datePTBRToDate($matches[0]))));
		}
		
		return [
			'status' => $scanEvents[0]['eventDescription'], 
			'last_update_date' => $lastUpdateDate, 
			'details' => $details, 
			"client_deadline" => $deadline ?? null,
		];
	}

	private function generateToken()
	{
		try {
			$response = Http::withHeaders(['X-locale' => 'pt_BR'])
				->asForm()
				->post('https://apis.fedex.com/oauth/token', [
					'grant_type' => 'client_credentials',
					'client_id' => env('FEDEX_CLIENT_ID'),
					'client_secret' => env('FEDEX_CLIENT_SECRET')
				]);
		}
		catch(\Exception $e) {
			$this->generateToken();
			return;
		}

		$this->writeApiCredentialDB('fedex', $response);
	}

    private function datePTBRToDate($dateString)
	{
	  return preg_replace(
		['/\sde\s/', '/Janeiro/', '/Fevereiro/', '/Março/', '/Abril/', '/Maio/', '/Junho/', '/Julho/', '/Agosto/', '/Setembro/', '/Outubro/', '/Novembro/', '/Dezembro/'],
		['-', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', ], 
		$dateString
	  );
	}
}