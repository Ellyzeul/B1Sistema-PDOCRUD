<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReadOrderAddressesByOrderNumberAction
{
    public function handle(Request $request)
    {
        $OrderNumber = $request->input('order_number');

        // $response = $this->searchByOrderNumber($OrderNumber);
        $response = $this->handleRequestBody($OrderNumber);

        return $response;
    }

    private function handleRequestBody(string $OrderNumber)
    {
        $items = [];

        $orderControl = $this->readOrderControlByOrderNumber($OrderNumber);
        $orderAdresses = $this->readOrderAdressesByOrderNumber($OrderNumber);

        return [
            "control" => $orderControl, 
            "addresses" => $orderAdresses
        ];

        // return [
        //     "id_company" => $orderControl[0]->id_company,
        //     "order" => [
        //       "number" => $orderControl[0]->online_order_number,
        //       "order_date" => $orderControl[0]->order_date,
        //       "expected_date" => $orderControl[0]->expected_date,
        //       "id_shop" => "204374622",
        //       "other_expenses" => "",
        //       "discount" => "",
        //       "freight" => "{$order->order_detail->shipping_price}",
        //       "total" => ""
        //     ],
        //     "client" => [
        //       "name" => "$order->client_firstname $order->client_lastname ($order->platform_vat_number)",
        //       "cpf_cnpj" => null,
        //       "phone" => "{$order->shipping_address->mobile}",
        //       "person_type" => "E",
        //       "email" => "$order->client_email",
        //       "address" => "{$order->shipping_address->address1}",
        //       "number" => "----",
        //       "postal_code" => "{$order->shipping_address->zipcode}",
        //       "uf" => "EX",
        //       "county" => "----",
        //       "city" => "{$order->shipping_address->city}",
        //       "complement" => "{$order->shipping_address->address3}",
        //       "country" => "PORTUGAL"
        //     ],
        //     "items" => $items
        // ];
    }

    private array $id_shops = [
        "FNAC-PT" => "204374622",
        "Amazon-Seline" => "203169907",
        "Estante-Seline" => "204374622",
        "MercadoLivre-Seline" => "204437827",
        "MagazineLuiza-Seline" => "203416940",
    ];

    private function readOrderAdressesByOrderNumber(string $OrderNumber)
    {
        $data = DB::table("order_addresses")
                    ->select("*")
                    ->where("online_order_number", "=", $OrderNumber)
                    ->first();
                    // ->toArray(); 

        return $data;
    }
    
    private function readOrderControlByOrderNumber(string $OrderNumber)
    {
        $data = DB::table('order_control')
            ->join('sellercentrals', 'order_control.id_sellercentral', '=', 'sellercentrals.id')
            ->select(
                'order_control.id',
                'order_control.id_company',
                'sellercentrals.name as sellercentral_name',
                'order_control.id_phase',
                'order_control.invoice_number',
                'order_control.online_order_number',
                'order_control.bling_number',
                'order_control.order_date',
                'order_control.expected_date',
                'order_control.isbn',
                'order_control.selling_price',
                'order_control.supplier_name',
                'order_control.purchase_date',
                'order_control.id_delivery_address',
                'order_control.supplier_purchase_number',
                'order_control.id_delivery_method',
                'order_control.tracking_code',
                'order_control.collection_code',
                'order_control.delivered_date',
                'order_control.ask_rating',
                'order_control.address_verified',
                'order_control.ready_to_6_2',
                'order_control.supplier_tracking_code',
                'order_control.id_supplier_delivery_method',
                'order_control.ship_date',
                'order_control.is_business_order',
                'order_control.ready_for_ship',
                'order_control.accepted',
            )
            ->where('order_control.online_order_number', '=', $OrderNumber)
            // ->whereNull('order_control.bling_number')
            ->get()
            ->toArray(); 
            
        return $data;
    }
}