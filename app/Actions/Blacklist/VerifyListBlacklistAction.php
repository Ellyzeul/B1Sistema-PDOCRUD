<?php namespace App\Actions\Blacklist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyListBlacklistAction
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

            $response = DB::table('blacklist')
                ->select('*')
                ->where('id_blacklist_type', '=', 1)
                ->where('content', '=', $isbn)
                ->get();

            $result[$isbn] = count($response) > 0;
        }

        return $result;
    }
}