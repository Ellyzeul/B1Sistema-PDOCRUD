<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReadOrdersAction
{
    public function handle(Request $request)
    {

        return $this->readOrders();
    }

    private function readOrders()
    {
        return "Ainda não foi refatorado";
    }

}