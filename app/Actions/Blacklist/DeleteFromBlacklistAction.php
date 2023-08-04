<?php namespace App\Actions\Blacklist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeleteFromBlacklistAction
{
    public function handle(Request $request)
    {
        $blacklistType = $request->input('blacklist_type');
        $content = $request->input('content');

        return $this->deleteFromBlacklist($blacklistType, $content);
    }

    private function deleteFromBlacklist(int $blacklistType, string $content)
    {
        $response = DB::table('blacklist')
            ->select('*')
            ->where('id_blacklist_type', '=', $blacklistType)
            ->where('content', '=', $content)
            ->delete();

        return $response === 1 
            ? ['Sucesso ao deletar da blacklist']
            : ['Erro ao deletar da blacklist'];   
    }
}