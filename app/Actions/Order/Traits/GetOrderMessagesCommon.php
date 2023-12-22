<?php namespace App\Actions\Order\Traits;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

trait GetOrderMessagesCommon
{
  private function getOrderNumbers(int $idSellercentral)
  {
    $orderNumbers = [];
    $results = Order::select('online_order_number')
      ->where('id_sellercentral', $idSellercentral)
      ->whereRaw('order_date BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()')
      ->get();

    foreach($results as $result) {
      array_push($orderNumbers, $result->online_order_number);
    }

    return $orderNumbers;
  }

  private function writeMessagesInDataBase(array $ordersMessages){
    // $error = false;

    try{
      $msgControlBodies = $this->getMessageControlBody($ordersMessages);
      
      if($msgControlBodies){
        $idSellercentral = $msgControlBodies[0]['id_sellercentral'];
        foreach($msgControlBodies as $msgControlBody){
          if($msgControlBody['online_order_number'] === null) continue;

          $messageControl = DB::table('order_tickets')
            ->upsert([
              'id_type' => $msgControlBody['id_type'],
              'online_order_number' => $msgControlBody['online_order_number'],
              'has_attachments' => $msgControlBody['has_attachments'], 
              'id_company' => $msgControlBody['id_company'], 
              'id_sellercentral' => $msgControlBody['id_sellercentral'], 
              'observation' => $msgControlBody['observation'], 
              'id_situation' => $msgControlBody['id_situation'], 
              'timestamp' => date("Y-m-d H:i:s", strtotime($msgControlBody['timestamp'])),
            ], ['has_attachments', 'observation', 'id_situation']);
          }
      }
    
      $msgContentBodies = $this->getMessageContentBody($ordersMessages);
      
      foreach($msgContentBodies as $msgContentBody){
        if($msgContentBody['id_order_ticket'] === null) continue;

        $responseMessageContent = DB::table('order_ticket_messages')
          ->upsert([
            'id_order_ticket' => $msgContentBody['id_order_ticket']->id,
            'online_message_number' => $msgContentBody['online_message_number'], 
            'message' => $msgContentBody['message'],
            'timestamp' => date("Y-m-d H:i:s", strtotime($msgContentBody['timestamp'])),
            'is_client_message' => $msgContentBody['is_client_message'],
          ], []);
        }

    } catch (\Exception $e) {
      var_dump($e->getMessage());
      // $error = true;
    }

    // var_dump($this->getOrderTicketsFromDB($idSellercentral));
    return $this->getOrderTicketsFromDB($idSellercentral);
  }

  private function getOrderTicketsFromDB(string $idSellercentral)
  {
    // $TicketsData = [];

    $ticketsData = DB::table('order_tickets')
      ->select(
        'order_tickets.id',
        'order_ticket_type.name as type',
        'order_tickets.online_order_number',
        'order_tickets.has_attachments',
        'order_tickets.id_company',
        'order_tickets.id_sellercentral',
        'order_tickets.observation',
        'order_ticket_situation.name as situation',
        'order_tickets.timestamp'
      )
      ->join(
        'order_ticket_situation',
        'order_tickets.id_situation', '=', 'order_ticket_situation.id'
      )
      ->leftJoin(
        'order_ticket_type',
        'order_tickets.id_type', '=', 'order_ticket_type.id'
      )      
      ->where('order_tickets.id_sellercentral', $idSellercentral)
      ->whereBetween('order_tickets.timestamp', [now()->subDays(60), now()])
      ->orderByDesc('order_tickets.timestamp')
      ->get()->toArray();
      
    // var_dump($messageTicket);
    // var_dump("tickets",$ticketsData);
    
    return $ticketsData;
  }

  private function getMessageControlBody(array $ordersMessages){
    $bodies = [];

    foreach($ordersMessages as $key => $orderMessages){
      array_push($bodies, [
        'id_type' => $orderMessages['type'] === 'order' ? 2 : 1, // 1 - offer e 2 - order
        'online_order_number' => $key,
        'has_attachments' => $orderMessages['has_attachments'] ?? 0, 
        'id_company' => $orderMessages['company'] === 'seline' ? 0 : 1, 
        'id_sellercentral' => $this->idSellercentral[$orderMessages['sellercentral']], 
        'observation' => "", 
        'id_situation' => 1,
        'timestamp' => date("Y-m-d H:i:s", strtotime($orderMessages["messages"][0]["date"])),
      ]);
    }

    return $bodies;
  }

  private function getMessageContentBody(array $ordersMessages){
    $msgs = [];

    foreach($ordersMessages as $key => $orderMessages){
      $ticketId = $this->getOrderTicketId($key);
    
      foreach($orderMessages['messages'] as $message){
        array_push($msgs, [
          'id_order_ticket' => $ticketId, 
          'online_message_number' => $message['id'],
          'message' => $message['text'],
          'timestamp' => $message['date'],
          'is_client_message' => $message['from'] === 'client' ? 1 : 0,
        ]);
      }
    }

    return $msgs;
  }

  private function verifyMissingMessages(string $orderNumber, array $ordersMessagesIds){
    $savedMessagesNumber = $this->getSavedMessagesNumber($orderNumber);

    $missingMessagesIds = array_diff($ordersMessagesIds, $savedMessagesNumber);
    
    return $missingMessagesIds;
  }

  private function getSavedMessagesNumber(string $orderNumber){
    $ticketId = $this->getOrderTicketId($orderNumber);

    if(!isset($ticketId)) return [];

    $savedMessagesNumber = DB::table('order_ticket_messages')
      ->select('online_message_number')
      ->where('id_order_ticket', '=', $ticketId)
      ->get(); 
    
    return $savedMessagesNumber;
  }

  private function getOrderTicketId(string $orderNumber){
    return DB::table('order_tickets')
      ->select('id')
      ->where('online_order_number', '=', $orderNumber)
      ->first();
  }

  private array $idSellercentral = [
    "fnac" => 8,
		"mercado-livre" => 9,
	];
}
