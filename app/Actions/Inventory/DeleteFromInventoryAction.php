<?php namespace App\Actions\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeleteFromInventoryAction
{
    public function handle(Request $request)
    {
        $isbn = $request->input('isbn');
        $location = $request->input('location');

        return $this->deleteFromBlacklist($isbn, $location);
    }

    private function deleteFromBlacklist(string $isbn, string $location)
    {
        $locationId = DB::table('inventory_location')
                    ->where('location', '=', $location)
                    ->value('id');

        $response = DB::table('inventory')
            ->select('*')
            ->where('isbn', '=', $isbn)
            ->where('location', '=', $locationId)
            ->delete();

        return $response === 1 
            ? ['Sucesso ao deletar do inventário']
            : ['Erro ao deletar do inventário'];   
    }
}