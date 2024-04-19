<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
	use HasFactory;

	protected $table = 'api_credentials';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public static function get(string $id): object
	{
		return json_decode(
			ApiCredential::where('id', $id)
				->first()
				->key
		);
	}

	public static function set(string $id, object $credential): void
	{
		$stringified = json_encode($credential);

		ApiCredential::where('id', $id)->update(['key' => $stringified]);
	}
}
