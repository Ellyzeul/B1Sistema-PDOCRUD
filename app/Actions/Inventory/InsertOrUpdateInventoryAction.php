<?php namespace App\Actions\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsertOrUpdateInventoryAction
{
    public function handle(Request $request)
    {
        $isbn = $request->input('isbn');
        $quantity = $request->input('quantity');
        $conditionId = $request->input('id_condition');
        $locationId = $request->input('id_location');
        $bookshelf = $request->input('bookshelf');
        $observation = $request->input('observation');

        return $this->insertOrUpdate($isbn, $quantity, $conditionId, $locationId, $bookshelf, $observation);
    }

    private function insertOrUpdate(string $isbn, int $quantity, int $conditionId, int $locationId, int|null $bookshelf, string|null $observation)
    {
        $response = DB::table('inventory')
            ->upsert([
                'isbn' => $isbn,
                'quantity' => $quantity,
                'id_condition' => $conditionId,
                'id_location' => $locationId,
                'bookshelf' => $bookshelf,
                'observation' => $observation,
            ], ['isbn', 'location']);
        
        if($response === 0) return ['message' => 'Erro ao inserir/atualizar no inventário'];
        
        return $response === 1 
                ? ['message' => 'Sucesso ao inserir no inventário']
                : ['message' => 'Sucesso ao atualizar no inventário'];
    }    
}