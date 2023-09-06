<?php namespace App\Actions\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsertOrUpdateInventoryAction
{
    public function handle(Request $request)
    {
        $isbn = $request->input('isbn');
        $quantity = $request->input('quantity');
        $condition = $request->input('condition');
        $location = $request->input('location');
        $bookshelf = $request->input('bookshelf');
        $observation = $request->input('observation');

        return $this->insertOrUpdate($isbn, $quantity, $condition, $location, $bookshelf, $observation);
    }

    private function insertOrUpdate(string $isbn, int $quantity, int $condition, int $location, int|null $bookshelf, string|null $observation)
    {
        $response = DB::table('inventory')
            ->upsert([
                'isbn' => $isbn,
                'quantity' => $quantity,
                'condition' => $condition,
                'location' => $location,
                'bookshelf' => $bookshelf,
                'observation' => $observation,
            ], ['isbn', 'location']);
        
        if($response === 0) return ['message' => 'Erro ao inserir/atualizar no inventário'];
        
        return $response === 1 
                ? ['message' => 'Sucesso ao inserir no inventário']
                : ['message' => 'Sucesso ao atualizar no inventário'];
    }    
}