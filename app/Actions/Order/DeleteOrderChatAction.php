<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeleteOrderChatAction
{
  public function handle(Request $request)
  {
    $orderTicketId = $request->input('order_ticket');
    $sellercentral = $request->input('sellercentral');    

    if(!$this->allowedServices[$sellercentral]) return ["Canal de venda não habilitado"];

    return $this->deleteOrderChat($orderTicketId, $sellercentral);
  }

  private function deleteOrderChat($orderTicketId, $sellercentral)
  {
    
  }

  private array $allowedServices = [
    'mercado-livre' => true,
    'fnac' => true,
  ];
}