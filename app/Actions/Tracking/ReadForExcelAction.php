<?php namespace App\Actions\Tracking;

use Illuminate\Support\Facades\DB;
use App\Models\PDOCrudWrapper;
use Illuminate\Http\Request;

class ReadForExcelAction
{
    public function handle(Request $request)
    {
		$orderNumbers = $request->input('order_numbers');

        return $this->readForExcel($orderNumbers);
    }

    private function readForExcel(array $orderNumbers)
    {
		$results = DB::table('order_control')
			->whereIn('online_order_number', $orderNumbers)
			->select(
				'id',
				'online_order_number',
				'tracking_code',
				'delivered_date',
			)
			->get();
		
		return [
			"columns" => $this->getColumnsNames(),
			"data" => $results
		];
    }

    private function getColumnsNames()
    {
        $pdocrud = new PDOCrudWrapper();

        return $pdocrud->getColumnsNames();
    }

}