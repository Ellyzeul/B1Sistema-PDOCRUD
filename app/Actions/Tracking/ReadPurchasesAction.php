<?php namespace App\Actions\Tracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReadPurchasesAction
{
    public function handle(Request $request)
    {

        return $this->readPurchases();
    }

    private function readPurchases()
    {
        return "Ainda não foi refatorado";
    }

}