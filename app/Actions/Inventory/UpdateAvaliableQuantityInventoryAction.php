<?php namespace App\Actions\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateAvaliableQuantityInventoryAction
{
    public function handle(Request $request)
    {
        $isbn = $request->input('isbn');

        return $this->updateAvaliableQuantityInventory($isbn);
    }

    private function updateAvaliableQuantityInventory(string $isbn)
    {
        $quantity = DB::table('inventory')
            ->where('isbn', '=', $isbn)
            ->where('quantity', '>', 0)
            ->get();
        
        if(count($quantity) > 0){
            $response = DB::table('inventory')
                ->where('isbn', '=', $isbn)
                ->where('id_location', '=', $quantity[0]->location) 
                ->decrement('quantity', 1);
        }

        return isset($response) 
                ? ["Sucesso ao associar o produto", 200]
                : ["Produto indisponível no inventário", 502];  
    }
}