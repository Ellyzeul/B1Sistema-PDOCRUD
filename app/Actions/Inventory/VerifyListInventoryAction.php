<?php namespace App\Actions\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyListInventoryAction
{
    public function handle(Request $request)
    {
        $isbns = explode(',', $request->input('isbns'));

        return $this->verifyFromList($isbns);
    }

    private function verifyFromList(array $isbns)
    {
        $result = [];

        foreach($isbns as $isbn) {
            $isbn = trim($isbn);

            $response = DB::table('inventory')
                ->select('*')
                ->where('isbn', '=', $isbn)
                ->where('quantity', '>', 0)
                ->get();

            $result[$isbn] = count($response) > 0;
        }

        return $result;
    }
}