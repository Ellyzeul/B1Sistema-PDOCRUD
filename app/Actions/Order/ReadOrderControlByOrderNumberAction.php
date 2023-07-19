<?php namespace App\Actions\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReadOrderControlByOrderNumberAction
{
    public function handle(Request $request)
    {
        $OrderNumber = $request->input('order_number');

        $response = $this->searchByOrderNumber($OrderNumber);

        return $response;
    }

    private function searchByOrderNumber(string $OrderNumber)
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
            ->whereNull('order_control.bling_number')
            ->get()
            ->toArray(); 
            
        return $data;
    }   
}