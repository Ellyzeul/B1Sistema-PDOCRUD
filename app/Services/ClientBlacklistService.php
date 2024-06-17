<?php

namespace App\Services;

use App\Models\ClientBlacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientBlacklistService
{
  public function readFromOrders(Request $request)
  {
    $addresses = DB::table('order_addresses')
      ->whereIn('online_order_number', $request->order_number ?? [])
      ->get(['online_order_number', 'cpf_cnpj', 'postal_code'])
      ->map(fn($address) => (object) [
        'online_order_number' => $address->online_order_number,
        'cpf_cnpj' => preg_replace('/[^\d]/', '', $address->cpf_cnpj ?? ''),
        'postal_code' => preg_replace('/[^\d]/', '', $address->postal_code ?? ''),
      ]);

    $blacklist = ClientBlacklist::whereIn(
      'key',
      $addresses->map(fn($address) => [$address->cpf_cnpj, $address->postal_code])->flatten(),
    )->get('key')->map(fn($blacklisted) => $blacklisted->key)->toArray();

    $list = [];
    foreach($addresses as $address) {
      $list[$address->online_order_number] = (in_array($address->cpf_cnpj, $blacklist) || in_array($address->postal_code, $blacklist));
    }

    return response(['list' => $list]);
  }
}
