<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\PDOCrudWrapper;
use App\Models\Services\Bling;
use App\Services\ThirdParty\Bling as ThirdPartyBling;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order_control';
    protected $primaryKey = 'id';
    public $timestamps = false;

    private array $deliveryMethods = [
        "PAC CONTRATO AG" => 2,
        "SEDEX CONTRATO AG" => 2,
        "SEDEX HOJE CONTRATO AG" => 2,
        "CORREIOS MINI ENVIOS CTR AG" => 2,
        "SEDEX 12 CONTRATO AG" => 2,
        "SEDEX 10 CONTRATO AG" => 2,
        "SEDEX CONTR GRAND FORMATO" => 2,
        "PAC CONTR GRAND FORMATO" => 2,
        "CARTA SIMPLES SELO E SE PCTE" => 2,
        "CARTA SIMPLES CHANCELA PCTE" => 2,
        "CARTA RG O4 CHANC ETIQUETA" => 2,
        "CARTA REG O4 MFD" => 2,
        "CARTA RG AR CONV O4 CHAN ETIQ" => 2,
        "CARTA REG AR CONV O4 MFD" => 2,
        "CARTA RG AR ELTR O4 CHANC ETIQ" => 2,
        "SEDEX HOJE EMPRESARIAL" => 2,
        "CARTA REG AR ELET O4 MFD" => 2,
        "TRANSFER LOG" =>2,
        ".Package" => 5,
        "Expresso" => 5,
        "Rodoviário" => 5,
        "Econômico" => 5,
        "DOC" => 5,
        "Corporate" => 5,
        ".Com" => 5,
        "Internacional" => 5,
        "Cargo" => 5,
        "Emergencial" => 5,
        "Pickup" => 5
    ];

    private array $blingAPIKeys = [
        0 => 'SELINE_BLING_API_TOKEN',
        1 => 'B1_BLING_API_TOKEN',
    ];

    public function getShipmentLabelData(string $orderId)
    {
        $order = DB::table('order_control')
            ->select('id_company', 'bling_number', 'id_delivery_method', 'tracking_code')
            ->where('id', $orderId)
            ->first();
        $company = DB::table('companies')
            ->select('id', 'name', 'company_name', 'cnpj', 'state_registration', 'municipal_registration')
            ->where('id', $order->id_company)
            ->first();
        $apikey = env(($this->blingAPIKeys[$order->id_company]));
        $blingResponse = $this->makeBlingOrderRequest($apikey, $order->bling_number);
        if(isset($blingResponse['error'])) return $blingResponse;

        $blingOrder = $blingResponse['retorno']['pedidos'][0]['pedido'];

        return [
            'company' => $company, 
            'id_delivery_method' => $order->id_delivery_method, 
            'tracking_code' => $order->tracking_code,
            'bling_data' => $blingOrder
        ];
    }

    public function updateReadForShip(array $toUpdate)
    {
        foreach($toUpdate as $registry) {
            DB::table('order_control')
                ->where('id', $registry['id'])
                ->update(['ready_for_ship' => $registry['ready_for_ship']]);
        }

        return [
            "message" => "Verificação de endereços atualizada com sucesso."
        ];
    }

    public function getTotalOrdersInPhase()
    {
        $results = DB::table('phases')
            ->select(
                'phases.id', 
                DB::raw('COUNT(order_control.id) as total'), 
                'phases.color'
            )
            ->leftJoin('order_control', 'phases.id', '=', 'order_control.id_phase')
            ->where('phases.id', '<', '7')
            ->orWhere('phases.id', '8.1')
            ->groupBy('phases.id')
            ->get();
        
        return $results;
    }

    public function getDataForAskRatingSpreadSheet()
    {
        $results = DB::table('order_control')
            ->select(
                'id', 
                'online_order_number', 
                'bling_number', 
                'id_company', 
                DB::raw('(SELECT name FROM companies WHERE id = id_company) as company'), 
                DB::raw('(SELECT name FROM sellercentrals WHERE id = id_sellercentral) as sellercentral'), 
                'id_phase'
            )
            ->where('id_phase', '6.2')
            ->orWhere('id_phase', '6.21')
            ->orderBy('id_phase')
            ->skip(0)
            ->take(50)
            ->get();
        
        $handled = array_map(function($result) {
            $blingResponse = Order::getBlingMessagingInfo(
                env($this->blingAPIKeys[$result->id_company]),
                $result->bling_number
            );
            if(isset($blingResponse['error'])) return [
                'id' => $result->id,
                'online_order_number' => $result->online_order_number,
                'company' => $result->company,
                'sellercentral' => $result->sellercentral,
                'id_phase' => $result->id_phase,
                'error' => true,
            ];
            [ $clientName, $clientEmail, $_, $bookName, $_ ] = $blingResponse;

            return [
                'id' => $result->id,
                'online_order_number' => $result->online_order_number,
                'company' => $result->company,
                'sellercentral' => $result->sellercentral,
                'url' => 'https://www.amazon.com.br/hz/feedback/?_encoding=UTF8&orderID=' . $result->online_order_number,
                'email' => $clientEmail,
                'client_name' => $clientName,
                'book_name' => $bookName,
                'id_phase' => $result->id_phase,
                'error' => false
            ];
        }, $results->toArray());

        return $handled;
    }


    public function getAddress(string $orderNumber)
    {
        $response = [];
        $response['bling'] = $this->getBlingEditData($orderNumber);

        $response['sellercentral'] = DB::table('order_addresses')
            ->where('online_order_number', $orderNumber)
            ->orderBy('postal_code', 'desc')
            ->first();
        if(isset($response['sellercentral'])) {
            $order = DB::table('order_control')
                ->select('id_sellercentral', 'id_company', 'expected_date', 'tracking_code', 'id_delivery_method')
                ->where('online_order_number', $orderNumber)
                ->first();
            $deliveryMethod = DB::table('delivery_methods')
                ->select('name')
                ->where('id', $order->id_delivery_method)
                ->first();
            $response['sellercentral']->id_sellercentral = $order->id_sellercentral;
            $response['sellercentral']->id_company = $order->id_company;
            $response['sellercentral']->delivery_method = $deliveryMethod->name ?? null;
            $response['sellercentral']->tracking_code = $order->tracking_code;
            $response['sellercentral']->expected_date = date('d/m/Y', strtotime($order->expected_date));
        }
        
        return $response;
    }

    private function getBlingEditData(string $orderNumber)
    {
        $order = DB::table('order_control')
            ->select('id_company', 'bling_number')
            ->where('online_order_number', $orderNumber)
            ->first();
        $bling = new Bling($order->id_company);

        $blingOrder = $bling->getOrder($order->bling_number);
        $blingOrderItems = $blingOrder->itens;
        $blingContact = $bling->getContactFromOrder($order->bling_number);
        $blingProducts = [];
        foreach($blingOrderItems as $item) {
            $productResponse = $bling->getProduct($item->produto->id);
            if(!isset($productResponse->error)) {
                array_push($blingProducts, $productResponse);
                continue;
            }
            $productResponse = $bling->getProductByCode($item->codigo);
            if(!isset($productResponse->error)) {
                $item->produto->id = $productResponse->id;
                array_push($blingProducts, $productResponse);
                continue;
            }

            $isbn = explode('_', $item->codigo)[1];
            $requestBody = [
                "nome" => $item->descricao,
                "codigo"=> $item->codigo,
                "unidade" => "UN",
                "tipo" => "P",
                "situacao" => "A",
                "preco" => $item->valor,
                "formato" => "S",
                "gtin" => $isbn,
                "gtinEmbalagem" => $isbn,
                "tributacao" => [
                    "origem" => 0,
                    "ncm" => "4901.99.00",
                    "cest"=> "28.064.00"
                ]
            ];
            $productResponse = $bling->postProduct($requestBody);
            var_dump($productResponse);
            array_push($blingProducts, array_merge($requestBody, ['id' => $productResponse->id]));
        }

        return [
            'update_data' => [
                'order' => $blingOrder,
                'contact' => $blingContact,
                'products' => $blingProducts,
            ],
            'id_bling' => $blingOrder->id,
            'bling_number' => $order->bling_number,
            'buyer_name' => $blingContact->nome,
            'recipient_name' => $blingOrder->transporte->etiqueta->nome ?? "",
            'person_type' => $this->getBlingPersonType($blingContact->tipo),
            'ie' => $blingContact->ie,
            'address' => $blingContact->endereco->geral->endereco,
            'number' => $blingContact->endereco->geral->numero,
            'complement' => $blingContact->endereco->geral->complemento,
            'city' => $blingContact->endereco->geral->municipio,
            'county' => $blingContact->endereco->geral->bairro,
            'email' => $blingContact->email,
            'cellphone' => $blingContact->celular,
            'landline' => $blingContact->telefone,
            'postal_code' => $blingContact->endereco->geral->cep,
            'uf' => $blingContact->endereco->geral->uf,
            'country' => $blingOrder->transporte->etiqueta->nomePais,
            'total_items' => array_reduce(array_map(fn($item) => $item->quantidade, $blingOrderItems), fn($acc, $cur) => $acc + $cur, 0),
            'total_value' => $blingOrder->total,
            'freight' => $blingOrder->transporte->frete ?? 0,
            'other_expenses' => $blingOrder->outrasDespesas,
            'discount' => $blingOrder->desconto->valor ?? 0,
            'expected_date' => $blingOrder->dataPrevista,
            'delivery_service' => $blingOrder->transporte->volumes[0]->servico ?? null,
            'observation' => $blingOrder->observacoes,
            'cpf_cnpj' => $blingContact->numeroDocumento,
            'items' => array_map(fn($item) => [
                'id' => $item->id,
                'sku' => $item->codigo,
                'title' => $item->descricao,
                'quantity' => $item->quantidade,
                'value' => $item->valor,
                'origin' => $item->tributacao->origem ?? null,
                'ncm' => $item->tributacao->ncm ?? null,
                'cest' => $item->tributacao->cest ?? null,
                'weight' => Order::select('weight')
                    ->where('online_order_number', $orderNumber)
                    ->where('isbn', explode('_', $item->codigo)[1])
                    ->first()
                    ->weight ?? 0
            ], $blingOrderItems),
        ];
    }

    private function getPersonType(string $personType)
    {
        if(\in_array($personType, ['Pessoa Física', 'F'])) return 'F';
        if(\in_array($personType, ['Pessoa Juridica', 'J'])) return 'J';
        if(\in_array($personType, ['Estrangeira', 'E'])) return 'E';

        return '';
    }

    public function putBlingOrder(array $blingData, int $companyId)
    {
        $bling = new Bling($companyId);
        $blingOrder = $blingData['update_data']['order'];
        $blingOrderItems = $this->handlePutBlingItems($blingData['items'], $blingOrder['numero']);
        $blingContact = $blingData['update_data']['contact'];
        $blingProducts = $blingData['update_data']['products'];
        $totalRaw = array_reduce(
            array_map(fn($item) => floatval(str_replace(',', '.', $item['value'])) * intval($item['quantity']), $blingData['items']), 
            fn($acc, $cur) => $acc + $cur
        );
        $total = floatval(number_format($totalRaw 
            + floatval(str_replace(',', '.', $blingData['freight'])) 
            + floatval(str_replace(',', '.', $blingData['other_expenses'])) 
            - floatval(str_replace(',', '.', $blingData['discounts'])), 2, '.', ''));
        $totalItems = array_reduce(
            array_map(fn($item) => intval($item['quantity']), $blingData['items']), 
            fn($acc, $cur) => $acc + $cur
        );
        $freight = floatval(str_replace(',', '.', $blingData['freight']));

        $blingOrder['id'] = $blingOrder['id'];
        $blingOrder['dataPrevista'] = $blingData['expected_date'];
        $blingOrder['contato']['tipoPessoa'] = $blingData['person_type'];
        $blingOrder['contato']['numeroDocumento'] = $blingData['cpf_cnpj'];
        $blingOrder['outrasDespesas'] = floatval(str_replace(",", ".", $blingData['other_expenses']));
        $blingOrder['desconto']['valor'] = floatval(str_replace(",", ".", $blingData['discounts']));
        $blingOrder['observacoes'] = $blingData['observation'];
        $blingOrder['total'] = $total;
        $blingOrder['totalProdutos'] = $totalRaw;
        $blingOrder['parcelas'][0]['valor'] = $totalRaw + $freight;
        $blingOrder['transporte']['frete'] = $freight;
        $blingOrder['transporte']['etiqueta']['nome'] = $blingData['recipient_name'];
        $blingOrder['transporte']['etiqueta']['endereco'] = $blingData['address'];
        $blingOrder['transporte']['etiqueta']['numero'] = $blingData['number'];
        $blingOrder['transporte']['etiqueta']['complemento'] = $blingData['complement'];
        $blingOrder['transporte']['etiqueta']['municipio'] = $blingData['city'];
        $blingOrder['transporte']['etiqueta']['uf'] = $blingData['uf'];
        $blingOrder['transporte']['etiqueta']['cep'] = $blingData['postal_code'];
        $blingOrder['transporte']['etiqueta']['bairro'] = $blingData['county'];
        $blingOrder['transporte']['etiqueta']['nomePais'] = $blingData['country'];
        $blingOrder['itens'] = array_map(function($item, $blingItem) {
            $value = floatval(str_replace(',', '.', $item['value']));
            $blingItem['quantidade'] = $item['quantity'];
            $blingItem['valor'] = $value;
            $blingItem['comissao']['base'] = $value;

            return $blingItem;
        }, $blingData['items'], $blingOrder['itens']);

        $blingContact['nome'] = $blingData['buyer_name'];
        $blingContact['tipo'] = $blingData['person_type'];
        $blingContact['numeroDocumento'] = $blingData['cpf_cnpj'];
        $blingContact['endereco']['geral']['endereco'] = $blingData['address'];
        $blingContact['endereco']['cobranca']['endereco'] = $blingData['address'];
        $blingContact['endereco']['geral']['numero'] = $blingData['number'] ?? '';
        $blingContact['endereco']['cobranca']['numero'] = $blingData['number'] ?? '';
        $blingContact['endereco']['geral']['complemento'] = $blingData['complement'] ?? '';
        $blingContact['endereco']['cobranca']['complemento'] = $blingData['complement'] ?? '';
        $blingContact['endereco']['geral']['municipio'] = $blingData['city'] ?? '';
        $blingContact['endereco']['cobranca']['municipio'] = $blingData['city'] ?? '';
        $blingContact['endereco']['geral']['uf'] = $blingData['uf'] ?? '';
        $blingContact['endereco']['cobranca']['uf'] = $blingData['uf'] ?? '';
        $blingContact['endereco']['geral']['cep'] = $blingData['postal_code'] ?? '';
        $blingContact['endereco']['cobranca']['cep'] = $blingData['postal_code'] ?? '';
        $blingContact['endereco']['geral']['bairro'] = $blingData['county'] ?? '';
        $blingContact['endereco']['cobranca']['bairro'] = $blingData['county'] ?? '';
        $blingContact['telefone'] = $blingData['landline'] ?? '';
        $blingContact['celular'] = $blingData['cellphone'] ?? '';
        $blingContact['pais']['nome'] = $blingData['country'] ?? '';

        $requestsBodyProducts = array_map(function($product, $item) {
            $product['gtin'] = $item['isbn'];
            $product['gtinEmbalagem'] = $item['isbn'];
            $product['tributacao']['origem'] = 0;
            $product['tributacao']['ncm'] = '4901.99.00';
            $product['tributacao']['cest'] = '28.064.00';

            return $product;
        }, $blingProducts, $blingOrderItems);

        $orderResponse = $bling->putOrder($blingOrder['id'], $blingOrder);
        $contactResponse = $bling->putContact($blingContact['id'], $blingContact);
        $productsResponse = [];
        foreach($requestsBodyProducts as $blingProduct) {
            array_push($productsResponse, $bling->putProduct($blingProduct['id'], $blingProduct));
        }

        return [
            'order' => $orderResponse,
            'contact' => $contactResponse,
            'products' => $productsResponse,
        ];
    }

    private function handlePutBlingItems(array $items, string $blingNumber)
    {
        foreach($items as $item) {
            $weight = $item['weight'];
            $isbn = $item['isbn'];
            unset($item['weight']);

            Order::where('bling_number', $blingNumber)
                ->where('isbn', $isbn)
                ->update(['weight' => $weight]);
        }

        return $items;
    }

    private function getBlingPersonType(string $personType)
    {
        if($personType === '') return "";
        if(in_array($personType, ['Pessoa Física', 'F'])) return "F";
        if(in_array($personType, ['Pessoa Jurídica', 'J'])) return "J";
        if(in_array($personType, ['Estrangeiro', 'E'])) return "E";

        throw new \Exception("Tipo de pessoa '$personType' não identificada como válida para o Bling...");
    }

    public static function getColumnsNames()
    {
        $pdocrud = new PDOCrudWrapper();

        return $pdocrud->getColumnsNames();
    }

    public function updateTrackingCode(string $companyId, string $orderId, string $blingNumber)
    {
        $apikey = env($this->blingAPIKeys[$companyId]);
        $blingResponse = Order::getBlingTrakingCodeRequest($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return $blingResponse['error'];

        $trackingCode = $blingResponse;

        DB::table('order_control')
            ->where('id', $orderId)
            ->update(['tracking_code' => $trackingCode]);

        return [[
            'message' => 'Código de rastreio atualizado!',
            'tracking_code' => $trackingCode
        ], 200];
    }

    public function updateTrackingService(string $companyId, string $orderId, string $blingNumber)
    {
        $apikey = env($this->blingAPIKeys[$companyId]);

        $blingResponse = Order::getBlingDeliveryMethodRequest($apikey, $blingNumber);
        if(isset($blingResponse['error'])) return ['message' => 'Erro na requisição de dados ao Bling...'];

        $deliveryMethod = $blingResponse;

        $deliveryMethodId = $this->deliveryMethods[$deliveryMethod];

        DB::table('order_control')
            ->where('id', $orderId)
            ->update(['id_delivery_method' => $deliveryMethodId]);

        return [[
            'message' => 'Forma de envio atualizada!',
            'delivery_method' => $deliveryMethodId,
        ], 200];
    }

    public function updateInvoiceNumber(string $orderId, string | null $invoiceNumber)
    {
        $databaseData = DB::table('order_control')
                ->select('id_company', 'bling_number')
                ->where('id', '=', $orderId)
                ->first();
                 
        $companyId = $databaseData->id_company; 
        $blingNumber = $databaseData->bling_number;

        if(isset($invoiceNumber)) return null;

        $data = $this->getInvoiceLink($companyId, $blingNumber);
        $invoice_number = $data['invoice_number'];
        $idBling = $data['id_bling'];
        $serie = $data['serie'];
        $linkFull = $data['link_full'];
        $linkSimplified = $data['link_simplified'];

        DB::table('order_control')
            ->where('id', $orderId)
            ->update(['invoice_number' => $invoice_number]);

        return [
            "id_bling" => $idBling,
            "invoice_number" => $invoice_number,
            "serie" => $serie,
            "link_full" => $linkFull,
            "link_simplified" => $linkSimplified,
        ];
    }
    
    public function getInvoiceLink(string $companyId, string $blingNumber)
    {
        $data = $this->getInvoiceNumberAndSerie($companyId, $blingNumber);
        $invoice_number = $data['invoice_number'];
        $serie = $data['serie'];
        $idInvoice = $data['id_invoice'];
        $idBling = $data['id_bling'];

        $apikey = env($this->blingAPIKeys[$companyId]);
        $linkDanfe = $this->getDanfeLink($apikey, $invoice_number, $serie);
        if(isset($linkDanfe['error'])) return $linkDanfe;

        $linkFull = $linkDanfe;

        return [
            "id_bling" => $idBling,
            "invoice_number" => $invoice_number,
            "serie" => $serie,
            "link_full" => $linkFull, 
            "link_simplified" => "https://www.bling.com.br/relatorios/danfe.simplificado.php?idNota1=$idInvoice"
        ];
    }

    private function getInvoiceNumberAndSerie(string $companyId, string $blingNumber)
    {
        $apikey = env($this->blingAPIKeys[$companyId]);
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        $orderResponse = (new ThirdPartyBling($companyId))->getOrder($blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        $invoice_number = $order['nota']['numero'] ?? null;
        $serie = $order['nota']['serie'] ?? null;

        preg_match("/[0-9]{5}$/", $invoice_number, $treated_arr);
        $treated_number = $treated_arr[0] ?? null;

        return [
            "id_bling" => $orderResponse->id, 
            "id_invoice" => $orderResponse->notaFiscal->id, 
            "invoice_number" => $treated_number,
            "serie" => $serie
        ];
    }

    private function array_some(array $array, callable $fn)
    {
        foreach($array as $value) {
            if($fn($value)) return true;
        }

        return false;
    }

    private function getBlingMessagingInfo(string $apikey, string $blingNumber)
    {
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        return [
            $order['cliente']['nome'],
            $order['cliente']['email'],
            $order['numeroPedidoLoja'],
            $order['itens'][0]['item']['descricao'],
            $order['cliente']['celular']
        ];
    }

    private function getBlingTrakingCodeRequest(string $apikey, string $blingNumber)
    {
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];
        $trackingCode = $order['transporte']['volumes'][0]['volume']['codigoRastreamento'] 
            ?? $order['transporte']['volumes'][0]['volume']['remessa']['numero']
            ?? null;

        return $trackingCode;         
    }

    private function getBlingDeliveryMethodRequest(string $apikey, string $blingNumber)
    {
        $response = $this->makeBlingOrderRequest($apikey, $blingNumber);
        if(isset($response['error'])) return $response;

        $order = $response['retorno']['pedidos'][0]['pedido'];

        return $order['transporte']['volumes'][0]['volume']['servico'];        
    }

    private function makeBlingOrderRequest(string $apikey, string $blingNumber)
    {
        $response = Http::get("https://bling.com.br/Api/v2/pedido/$blingNumber/json?apikey=$apikey");
        if(!$response->ok()) return ["error" => [
            ["message" => "Erro na requisição de dados no Bling. Tente novamente mais tarde..."], 
            500
        ]];

        return $response;
    }

    private function getDanfeLink(string $apikey, string | null $invoice_number, string | null $serie)
    {
        if(!isset($invoice_number)) return null;
        $response = Http::get("https://bling.com.br/Api/v2/notafiscal/$invoice_number/$serie/json/?apikey=$apikey");
        if(!$response->ok()) return ["error" => [
            ["message" => "Erro na requisição de dados no Bling. Tente novamente mais tarde..."], 
            500
        ]];

        return $response['retorno']['notasfiscais'][0]['notafiscal']['linkDanfe'];
    }
}