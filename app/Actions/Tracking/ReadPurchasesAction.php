<?php namespace App\Actions\Tracking;

use Illuminate\Support\Facades\DB;

class ReadPurchasesAction
{
    public function handle()
    {

        return $this->readPurchases();
    }

    private function readPurchases()
    {
		$results = DB::table('purchase_trackings')
			->join('order_control', 'purchase_trackings.tracking_code', '=', 'order_control.supplier_tracking_code')
			->select(
				'purchase_trackings.tracking_code',
				(DB::raw('(SELECT name FROM supplier_delivery_methods WHERE id = order_control.id_supplier_delivery_method) as delivery_method')),
				'order_control.online_order_number',
				'purchase_trackings.status',
				'purchase_trackings.last_update_date',
				'purchase_trackings.details',
				'order_control.expected_date',
				'purchase_trackings.delivery_expected_date',
				'purchase_trackings.deadline',
				'purchase_trackings.api_calling_date',
				'purchase_trackings.observation',
			)
			->where('order_control.id_phase', '=', '3.1')
			->get();
		
		return $results;        
    }

}