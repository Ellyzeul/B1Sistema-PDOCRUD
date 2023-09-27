<?php namespace App\Actions\Tracking;

use Illuminate\Support\Facades\DB;

class ReadOrdersAction
{
    public function handle()
    {
        return $this->readOrders();
    }

    private function readOrders()
    {
		$results = DB::table('trackings')
			->join('order_control', 'trackings.tracking_code', '=', 'order_control.tracking_code')
			->select(
				'trackings.tracking_code',
				(DB::raw('(SELECT name FROM delivery_methods WHERE id = order_control.id_delivery_method) as delivery_method')),
				'order_control.online_order_number',
				'trackings.status',
				'trackings.last_update_date',
				'trackings.details',
				'order_control.expected_date',
				'trackings.delivery_expected_date',
				'trackings.client_deadline',
				'trackings.api_calling_date',
				'trackings.observation',
			)
			->where('order_control.id_phase', '=', '5.1')
			->orWhere('order_control.id_phase', '=', '5.2')
			->get();
		
		return $results;
    }

}