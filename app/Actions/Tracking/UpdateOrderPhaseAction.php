<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateOrderPhaseAction
{
    public function handle(Request $request)
    {
        $orderNumber = $request->input('order_number');
        $deliveredDate = $request->input('delivered_date');

        return $this->updateOrderPhase($orderNumber, $deliveredDate);
    }

    private function updateOrderPhase($orderNumber, $deliveredDate)
    {
        $response = DB::table('order_control')
            ->where('online_order_number','=', $orderNumber)
            ->where('id_phase', '=', '5.1')
            ->update([
                'id_phase' => '6.1', 
                'delivered_date' => $deliveredDate
            ]);

        return $response
            ? ["Sucesso ao atualizar $orderNumber para a fase 6.1", 200]
            : ["Erro ao atualizar $orderNumber para a fase 6.1", 502];
    }

}