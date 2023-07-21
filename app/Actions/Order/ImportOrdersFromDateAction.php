<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use App\Actions\Order\ImportOrdersFromDate\ImportFromFNACAction as FNAC;
use App\Actions\Order\ImportOrdersFromDate\ImportFromMercadoLivreAction as MercadoLivre;

class ImportOrdersFromDateAction
{
  private array $sellercentrals = [
    ['id_company' => 0, 'channel' => 'mercado-livre'], 
    ['id_company' => 1, 'channel' => 'mercado-livre'], 
    ['id_company' => 0, 'channel' => 'fnac'], 
  ];

  public function handle(Request $request)
  {
    $fromDate = $request->input('from');
    $response = [];

    foreach($this->sellercentrals as $sellercentral) {
      $response = array_merge($response, $this->importFromSellercentral(
        $fromDate, 
        $sellercentral['id_company'], 
        $sellercentral['channel'], 
      ));
    }

    return $response;
  }

  private function importFromSellercentral(string $fromDate, int $idCompany, string $channel): array
  {
    if($channel === 'mercado-livre') return (new MercadoLivre())->handle($fromDate, $idCompany);
    if($channel === 'fnac') return (new FNAC())->handle($fromDate, $idCompany);

    throw new \Exception("Sales channel unknown: $channel");
  }
}
