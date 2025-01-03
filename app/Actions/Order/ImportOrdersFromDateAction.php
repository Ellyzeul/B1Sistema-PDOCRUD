<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use App\Actions\Order\ImportOrdersFromDate\ImportFromFNACAction as FNAC;
use App\Actions\Order\ImportOrdersFromDate\ImportFromMercadoLivreAction as MercadoLivre;
use App\Actions\Order\ImportOrdersFromDate\ImportFromNuvemshopAction as Nuvemshop;

class ImportOrdersFromDateAction
{
  private array $sellercentrals = [
    ['id_company' => 0, 'channel' => 'fnac'], 
    ['id_company' => 0, 'channel' => 'nuvemshop'], 
  ];

  public function handle(Request $request)
  {
    $fromDate = $request->input('from');
    $responses = [];
    
    foreach($this->sellercentrals as $sellercentral) {
      $company = $sellercentral['id_company'] === 0 ? "Seline" : "B1";
      try {
        $response = $this->importFromSellercentral(
          $fromDate, 
          $sellercentral['id_company'], 
          $sellercentral['channel'], 
        );
        $status = 'success';
      }
      catch(\Exception $_) {
        $status = 'error';
        $response = [];
        $errMsg = "Erro no canal de venda: {$sellercentral['channel']} - $company";
      }
      $responses[] = [
        'status' => $status, 
        'content' => $response, 
        'err_msg' => $errMsg ?? null, 
      ];
    }

    return $responses;
  }

  private function importFromSellercentral(string $fromDate, int $idCompany, string $channel): array
  {
    if($channel === 'mercado-livre') return (new MercadoLivre())->handle($fromDate, $idCompany);
    if($channel === 'fnac') return (new FNAC())->handle($fromDate, $idCompany);
    if($channel === 'nuvemshop') return (new Nuvemshop())->handle($fromDate, $idCompany);

    throw new \Exception("Sales channel unknown: $channel");
  }
}
