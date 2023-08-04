<?php namespace App\Actions\Blacklist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsertOrUpdateBlacklistAction
{
    public function handle(Request $request)
    {
        $blacklistType = $request->input('blacklist_type');
        $content = $request->input('content');
        $observation = $request->input('observation');

        return $this->insertOrUpdate($content, $observation, $blacklistType);
    }

    
    private function insertOrUpdate(string $content, string|null $observation, int $blacklistType)
    {
        $response = DB::table('blacklist')
            ->upsert([
                'content' => $content,
                'observation' => $observation,
                'id_blacklist_type' => $blacklistType
            ], ['content']);
        
        if($response === 0) return ['message' => 'Erro ao inserir/atualizar na blacklist'];
        
        return $response === 1 
                ? ['message' => 'Sucesso ao inserir na blacklist']
                : ['message' => 'Sucesso ao atualizar na blacklist'];
    }
}