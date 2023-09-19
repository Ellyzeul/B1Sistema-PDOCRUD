<?php namespace App\Services;

use Illuminate\Http\Request;

use App\Actions\Tracking\UpdateOrderPhaseAction;

class TrackingService
{
    public function updateOrderPhaseAction(Request $request)
    {
        return (new UpdateOrderPhaseAction())->handle($request);
    }
}