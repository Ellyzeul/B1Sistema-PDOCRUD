<?php namespace App\Actions\Inventory;

use App\Services\BlacklistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchInventoryAction
{
    public function handle(Request $request)
    {
        $isbn = $request->input('isbn');

        return $this->search($isbn);
    }

    private function search(string $isbn)
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
            ->join('inventory_condition', 'inventory.id_condition', '=', 'inventory_condition.id')
            ->join('inventory_location', 'inventory.id_location', '=', 'inventory_location.id')
            ->where('inventory.isbn', '=', $isbn)
            ->where('inventory.quantity', '>', 0)
            ->get()
            ->toArray();
        
        $blacklist = $this->getBlacklist($response);
        
        foreach($response as $item) {
            $item->blacklisted = $blacklist[$item->isbn];
        }
    
        return $response; 
    }

    private function getBlackList(array $inventory)
    {
        $isbns = join(',', array_map(fn($item) => $item->isbn, $inventory));

        return (new BlacklistService())->verifyListBlacklist(new Request([ 'isbns' => $isbns ]));
    }
}