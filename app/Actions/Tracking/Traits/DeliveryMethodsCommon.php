<?php namespace App\Actions\Tracking\traits;

use Illuminate\Support\Facades\DB;

trait DeliveryMethodsCommon
{
	private function readApiCredentialDB(string $id)
	{
		$json = DB::table('api_credentials')
			->select('key')
			->where('id', $id)
			->first()
			->key;

		return json_decode($json);
	}

    private function writeApiCredentialDB(string $id, string | null $key)
	{
		DB::table('api_credentials')
			->upsert([
				'id' => $id,
				'key' => $key
			], ['key']);
	}

	private function existsApiCredentialDB(string $id)
	{
		return DB::table('api_credentials')
			->where('id', $id)
			->exists();
	}

	private function getOrderIdAndcompanyIdByTrackingCode(string $trackingCode)
	{
		$data = DB::table('order_control')
			->select('id_company', 'online_order_number')
			->where('tracking_code', $trackingCode)
			->first();

		return $data;
	}
}