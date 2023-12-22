<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GetOrderChatAction
{
  public function handle(Request $request)
  {
    $orderTicketId = $request->input('order_ticket');
    $orderNumber = $request->input('online_order_number');

    return $this->getOrderChat($orderTicketId, $orderNumber);
  }

  private function getOrderChat(string $orderTicketId, string $orderNumber)
  {
    $orderInfo = [];

    $ticketInfo = DB::table('order_tickets')
      ->select(
        'order_tickets.id', 
        'order_ticket_type.name as type', 
        'order_tickets.online_order_number',
      )
      ->join('order_ticket_type', 'order_tickets.id_type', '=', 'order_ticket_type.id')
      ->where('order_tickets.id', $orderTicketId)
      ->first();

    if($ticketInfo->type === 'Pedido') $orderInfo = $this->getOrderInfo($orderNumber);

    return [
      'chat' => $this->getChatMessages($orderTicketId),
      'order_info' => $orderInfo,
    ];
  }

  private function getOrderInfo(string $orderNumber)
  {
    $data = DB::table('order_control')
      ->select(
        'online_order_number',
        'id_company',
        'sellercentrals.name as sellercentral',
        'order_date',
        'expected_date',
        'delivered_date',
        'id_delivery_method',
        'tracking_code',
        'id_phase',
        'invoice_number',
        'bling_number',
        'supplier_name',
        'supplier_tracking_code',
        'id_supplier_delivery_method',
        'shipping_box_number',
        'status_on_shipping_box',
      )
      ->join('sellercentrals', 'order_control.id_sellercentral', '=', 'sellercentrals.id')
      ->where('online_order_number', $orderNumber)
      ->first();

    return [
      'online_order_number'=> $data->online_order_number,
      'id_company' => $data->id_company,
      'sellercentral' => $data->sellercentral,
      'order_date' => isset($data->order_date) 
        ? date('d/m/Y', strtotime($data->order_date)) : null,
      'expected_date' => isset($data->expected_date) 
        ? date('d/m/Y', strtotime($data->expected_date)) : null,
      'delivered_date' => isset($data->delivered_date) 
        ? date('d/m/Y', strtotime($data->delivered_date)) : null,
      'days_for_shipping' => isset($data->expected_date)
        ? Carbon::now()->diffInDays(Carbon::parse(str_replace('/', '-', $data->expected_date)), false)
        : null, 
      'delivery_method' => $data->id_delivery_method,
      'tracking_code' => $data->tracking_code,
      'id_phase' => $data->id_phase,
      'invoice_number' => $data->invoice_number,
      'bling_number' => $data->bling_number,
      'supplier_name' => $data->supplier_name,
      'supplier_tracking_code' => $data->supplier_tracking_code,
      'shipping_box_number' => $data->shipping_box_number,
      'shipped_by_enviadotcom' => $data->status_on_shipping_box === 0 ? 'Não' : 'Sim',
    ];
  }

  private function getChatMessages(string $orderTicketId)
  {
    return DB::table('order_ticket_messages')
        ->where('id_order_ticket', $orderTicketId)
        ->orderBy('timestamp', 'ASC')
        ->get();
  }
}