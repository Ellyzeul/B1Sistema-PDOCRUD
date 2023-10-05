<?php namespace App\Services;

use Illuminate\Http\Request;
use App\Actions\Tracking\ReadOrdersAction;
use App\Actions\Tracking\ReadPurchasesAction;
use App\Actions\Tracking\ReadForExcelAction;
use App\Actions\Tracking\UpdateOrderPhaseAction;
use App\Actions\Tracking\UpdateFieldAction;
use App\Actions\Tracking\UpdateAllAction;
use App\Actions\Tracking\UpdateOrInsertOrderTrackingAction;
use App\Actions\Tracking\UpdateOrInsertPurchaseTrackingAction;
use App\Actions\Tracking\ConsultPriceAndShippingAction;
use App\Actions\Tracking\ConsultPostalCodeAction;
use App\Actions\Tracking\GetEnviaDotComShipmentLabelAction;

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
    
    public function updateOrInsertOrderTracking(Request $request)
    {
        $trackingCode = $request->input('tracking_code');
        $deliveryMethod = $request->input('delivery_method');

        return (new UpdateOrInsertOrderTrackingAction())->handle($trackingCode, $deliveryMethod);
    }  

    public function updateOrInsertPurchaseTracking(Request $request)
    {
        $trackingCode = $request->input('tracking_code');
        $deliveryMethod = $request->input('delivery_method');

        return (new UpdateOrInsertPurchaseTrackingAction())->handle($trackingCode, $deliveryMethod);
    }  
    
    public function updateOrderPhase(Request $request)
    {
        return (new UpdateOrderPhaseAction())->handle($request);
    }
    
    public function updateField(Request $request)
    {
        return (new UpdateFieldAction())->handle($request);
    }

    public function  updateAll(Request $request)
    {
        return (new  UpdateAllAction())->handle($request);
    }

    public function consultPriceAndShipping(Request $request)
    {
        return (new ConsultPriceAndShippingAction())->handle($request);
    }

    public function consultPostalCode(Request $request)
    {
        return (new ConsultPostalCodeAction())->handle($request);
    }
    
    public function getEnviaDotComShipmentLabel(Request $request)
    {
        return (new GetEnviaDotComShipmentLabelAction())->handle($request);
    }
}