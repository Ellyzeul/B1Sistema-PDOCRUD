<?php namespace App\Services;

use Illuminate\Http\Request;

use App\Actions\Tracking\ReadOrdersAction;
use App\Actions\Tracking\ReadPurchasesAction;
use App\Actions\Tracking\ReadForExcelAction;
use App\Actions\Tracking\UpdateOrderPhaseAction;

class TrackingService
{
    public function readOrders()
    {
        return (new ReadOrdersAction())->handle();
    }    

    public function readPurchases()
    {
        return (new ReadPurchasesAction())->handle();
    }    

    public function readForExcel(Request $request)
    {
        return (new ReadForExcelAction())->handle($request);
    }  
    
    public function updateOrderPhase(Request $request)
    {
        return (new UpdateOrderPhaseAction())->handle($request);
    }
}