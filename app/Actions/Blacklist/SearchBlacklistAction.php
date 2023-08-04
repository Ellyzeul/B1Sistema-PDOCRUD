<?php namespace App\Actions\Blacklist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchBlacklistAction
{
    public function handle(Request $request)
    {
        $blacklistType = $request->input('blacklist_type');
        $content = $request->input('content');

        return $this->search($blacklistType, $content);
    }

    private function search(int $blacklistType, string|null $content)
    {
        if($content === null) {
            $response = DB::table('blacklist')
            ->where('id_blacklist_type', $blacklistType)
            ->orderBy('id') 
            ->take(20) 
            ->get();            

        }else{
            $response = DB::table('blacklist')
                ->select('*')
                ->where('id_blacklist_type', '=', $blacklistType)
                ->where('content', '=', $content)
                ->get();
        }
    
        return $response;
    }
}