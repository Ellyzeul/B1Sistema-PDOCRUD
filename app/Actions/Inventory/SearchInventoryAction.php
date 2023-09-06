<?php namespace App\Actions\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchInventoryAction
{
    public function handle(Request $request)
    {
        $isbn = $request->input('isbn');

        return $this->seach($isbn);
    }

    private function seach(string $isbn)
    {
        $response = DB::table('inventory')
            ->select(
                'inventory.isbn', 
                'inventory.quantity', 
                'inventory_condition.condition', 
                'inventory_location.location', 
                'inventory.bookshelf', 
                'inventory.observation'
            )
            ->join('inventory_condition', 'inventory.condition', '=', 'inventory_condition.id')
            ->join('inventory_location', 'inventory.location', '=', 'inventory_location.id')
            ->where('inventory.isbn', '=', $isbn)
            ->where('inventory.quantity', '>', 0)
            ->get();
    
        return $response; 
    }
}