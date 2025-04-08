<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use App\Actions\Tracking\ReadPurchasesAction;
use App\Actions\Tracking\ReadOrdersAction;
use App\Actions\Tracking\UpdateOrInsertOrderTrackingAction;
use App\Actions\Tracking\UpdateOrInsertPurchaseTrackingAction;

class UpdateAllAction
{
    public function handle(Request $request)
    {
		$isPurchases = $request->input('is_purchases');

        return $this->updateAll($isPurchases);
    }

	/*
	* @return [
	* 	error_code: 0 - sucess, 1 - warning or 2 - error,
	*	totalErrors: total number of errors
	* ]
	*/
	private function updateAll(string $isPurchases){		
		if($isPurchases==='1'){
			$rows = (new ReadPurchasesAction())->handle();
			$totalErrors = 0; 
			$errorCode = 0; 
			foreach($rows as $row) {
				try {
					$response = (new UpdateOrInsertPurchaseTrackingAction())->handle($row->tracking_code, $row->delivery_method);
				}
				catch(\Exception) {
					$totalErrors+=1;
					continue;
				}
			}
		}else {
			$rows = (new ReadOrdersAction())->handle();
			$totalErrors = 0; 
			$errorCode = 0; 
			foreach($rows as $row) {
				try {
					$response = (new UpdateOrInsertOrderTrackingAction())->handle($row->tracking_code, $row->delivery_method);
				}
				catch(\Exception) {
					$totalErrors+=1;
					continue;
				}
			}
		}

		if($totalErrors != 0) $errorCode = 1;
		if($totalErrors == count($rows)) $errorCode = 2;

		return isset($response)
			? [["error_code" => $errorCode, "total_errors" => $totalErrors], 200]
			: ["Erro na atualização", 500];
	}    
}