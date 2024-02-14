<?php namespace App\Services\ThirdParty;

class FNAC
{
  const CREDENTIALS = [
    'pt-0' => [ 'partner_id' => 'FNAC_PT_SELINE_PARTNER_ID', 'shop_id' => 'FNAC_PT_SELINE_SHOP_ID', 'key' => 'FNAC_PT_SELINE_KEY' ], 
    'es-0' => [ 'partner_id' => 'FNAC_ES_SELINE_PARTNER_ID', 'shop_id' => 'FNAC_ES_SELINE_SHOP_ID', 'key' => 'FNAC_ES_SELINE_KEY' ], 
  ];

  private string $baseUrl = 'https://vendeur.fnac.com/api.php';
  private int $resultsCount = 200;
  private string $partnerId;
  private string $shopId;
  private string $key;
  private string $token;

  public function __construct(string $country, int $idCompany)
  {
    $key = "$country-$idCompany";

    try {
      $credential = FNAC::CREDENTIALS[$key];
    }
    catch(\Exception $_) {
      throw new \Exception("Country $country and company id $idCompany doesn't exists on FNAC");
    }

    $this->partnerId = env($credential['partner_id']);
    $this->shopId = env($credential['shop_id']);
    $this->key = env($credential['key']);
  }

  /**
   * Busca mensagens do pedido
   */

  public function messagesQuery(?string $orderId = null, ?string $messageType = null)
  {
    $this->authenticate();

    $partnerId = $this->partnerId;
    $shopId = $this->shopId;
    $token = $this->token;

    $orderIdFilter = isset($orderId) ? "<order_fnac_id>$orderId</order_fnac_id>" : '';
    $messageTypeFilter = isset($messageType) ? "<message_type>$messageType</message_type>" : '';

    $xml = <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <messages_query 
      xmlns="http://www.fnac.com/schemas/mp-dialog.xsd" 
      shop_id="$shopId" 
      partner_id="$partnerId" 
      token="$token"
    >
      $orderIdFilter
      $messageTypeFilter
      <message_from_types>
        <from_type>SELLER</from_type>
        <from_type>CLIENT</from_type>
        <from_type>CALLCENTER</from_type>
        <from_type>CLIENT</from_type>
      </message_from_types>
    </messages_query>
    XML;

    $messagesQuery = $this->postXML('/messages_query', $xml);
    $messages = [];
    foreach($messagesQuery->message as $message) array_push($messages, $message);

    return $messages;
  }

  /**
   * Posta mensagens no pedido
   */

  public function messagesUpdate(string $messageId, string $text, ?string $action='reply', ?string $subject=null)
  {
    $this->authenticate();

    $partnerId = $this->partnerId;
    $shopId = $this->shopId;
    $token = $this->token;
    $markAsRead = $action === 'reply' 
      ? "<message action=\"mark_as_read\" id=\"$messageId\"/>"
      : '';
    $messageSubject = isset($subject) 
      ? "<message_subject><![CDATA[$subject]]></message_subject>"
      : '';

    $xml = <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <messages_update 
      xmlns="http://www.fnac.com/schemas/mp-dialog.xsd" 
      shop_id="$shopId" 
      partner_id="$partnerId" 
      token="$token"
    >
      $markAsRead
      <message action="$action" id="$messageId">
        <message_to><![CDATA[CLIENT]]></message_to>
        $messageSubject
        <message_description><![CDATA[$text]]></message_description>
      </message>
    </messages_update>
    XML;

    $messagesUpdate = $this->postXML('/messages_update', $xml);

    return [
      'success' => "{$messagesUpdate->attributes()->status}" === 'OK', 
      'message' => $messagesUpdate
    ];
  }

  /**
   * Busca pedidos através da data
   */

  public function ordersQuery(
    string | null $fromDate=null, 
    string | null $dateType=null, 
    array | null $ordersId=null, 
    array | null $states=null, 
  )
  {
    $fromDate = $fromDate . 'T00:00:00+03:00';
    $filters = $this->getOrdersQueryFilters(1, $fromDate, $dateType, $ordersId, $states);
    $requestBody = $this->getXMLBody('orders_query', $filters);

    $ordersQuery = $this->postXML('/orders_query', $requestBody);
    $response = [];
    foreach($ordersQuery->order as $order) array_push($response, $order);

    return $response;
  }

  private function getOrdersQueryFilters(
    int $paging=1, 
    string | null $fromDate=null, 
    string | null $dateType=null, 
    array | null $ordersId=null, 
    array | null $states=null, 
  )
  {
    $filters = ["<paging>$paging</paging>"];

    if(isset($fromDate) && isset($dateType)) {
      $toDate = date('Y-m-d', strtotime('+1 day')) . 'T00:00:00+03:00';

      array_push($filters, $this->parseFiltersListToXML(
        'date', 
        ['min', 'max'], 
        [$fromDate, $toDate], 
        ['type' => $dateType], 
      ));
    }

    if(isset($ordersId)) array_push($filters, $this->parseFiltersListToXML(
      'orders_fnac_id', 
      'order_fnac_id', 
      $ordersId, 
    ));

    if(isset($states)) array_push($filters, $this->parseFiltersListToXML(
      'states', 
      'state', 
      $states, 
    ));

    return $filters;
  }

  private function parseFiltersListToXML(
    string $constraint, 
    string | array $elements, 
    array $list, 
    array | null $attributes=null
  )
  {
    $parsed = '';

    if(gettype($elements) == 'array') {
      $length = count($list);
      if(count($elements) != $length) {
        throw new \Exception('Elements quantity are different from list quantity');
      }

      $parsing = [];
      for($i = 0; $i < $length; $i++) {
        $element = $elements[$i];
        $item = $list[$i];
        array_push($parsing, "<$element>$item</$element>");
      }

      $parsed = join('', $parsing);
    }
    else {
      $parsed = join('', array_map(fn($item) => "<$elements>$item</$elements>", $list));
    }

    $taggedAttr = '';
    if(isset($attributes)) {
      $taggedList = [];
      foreach($attributes as $key => $val) {
        array_push($taggedList, "$key=\"$val\"");
      }

      $taggedAttr = join(' ', $taggedList);
    }

    return "<$constraint $taggedAttr>$parsed</$constraint>";
  }

  public function acceptOrder(string $orderNumber)
  {
    $this->authenticate();

    $partnerId = $this->partnerId;
    $shopId = $this->shopId;
    $token = $this->token;

    $response = $this->postXML('/orders_update', <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <orders_update 
      xmlns="http://www.fnac.com/schemas/mp-dialog.xsd" 
      shop_id="$shopId" 
      partner_id="$partnerId" 
      token="$token"
    >
      <order order_id="$orderNumber" action="accept_all_orders">
        <order_detail>
          <action>Accepted</action>
        </order_detail>
      </order>
    </orders_update>
    XML);

    $order = $response->order;
    return [
      'success' => "$order->status", 
      'current_status' => "$order->state", 
    ];
  }

  // Métodos privados
  private function authenticate()
  {
    $partnerId = $this->partnerId;
    $shopId = $this->shopId;
    $key = $this->key;
    $requestBody = <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <auth xmlns="http://www.fnac.com/schemas/mp-dialog.xsd">
      <partner_id>$partnerId</partner_id>
      <shop_id>$shopId</shop_id>
      <key>$key</key>
    </auth>
    XML;

    $response = $this->postXML('/auth', $requestBody);

    $this->token = $response->token;
  }

  private function getXMLBody(string $service, array $elements, bool $useResultsCount = true)
  {
    if(!isset($this->token)) $this->authenticate();

    $resultsCountStr = $useResultsCount ? "results_count=\"$this->resultsCount\"" : '';
    $partnerId = $this->partnerId;
    $shopId = $this->shopId;
    $token = $this->token;
    $children = join('', $elements);
    $attributes = 
        "$resultsCountStr "
      . "partner_id=\"$partnerId\" "
      . "shop_id=\"$shopId\" "
      . "token=\"$token\" "
      . "xmlns=\"http://www.fnac.com/schemas/mp-dialog.xsd\"";

    return <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <$service $attributes>$children</$service>
    XML;
  }

  private function postXML(string $endpoint, string $requestBody)
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->baseUrl . "/$endpoint");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: text/xml', 
      'Content-Length: ' . strlen($requestBody), 
      'Connection: close', 
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return simplexml_load_string($response, \SimpleXMLElement::class, LIBXML_NOCDATA);
  }
}
