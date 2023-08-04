<?php namespace App\Actions\Blacklist;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReadBlacklistFromIntervalAction
{
    public function handle(Request $request)
    {
        $start = (int) $request->input('start');
        $blacklistType = $request->input('blacklist_type');
        $isRight = $request->input('is_right');

        $result = $this->search($start, $blacklistType, $isRight);

        return $result;
    }

    
    private function search(int $start, int $blacklistType, bool $isRight)
    {
        $operator = $isRight ? '>=' : '<=';
        
        $query = DB::table('blacklist')
            ->where('id_blacklist_type', $blacklistType)
            ->where('id', $operator, $start)
            ->orderBy('id', $isRight ? 'asc' : 'desc')
            ->take(20);
        
        $data = $query->get();
        $totalElements = $query->count();
        
        if ($start > $totalElements) {
            return [
                'data' => [], 
                'totalElements' => $totalElements,
                'remainingElementsRight' => 0,
                'remainingElementsLeft' => 0,
            ];
        }

        $remainingElementsRight = max(0, $totalElements - count($data));
        
        $remainingElementsLeft = $isRight ? max(0, $start - 1) : 0;
        
        return [
            'data' => $data,
            'totalElements' => $totalElements,
            'remainingElementsRight' => $remainingElementsRight,
            'remainingElementsLeft' => $remainingElementsLeft,
        ];
    }
    
    // private function search(int $start, int $blacklistType, bool $isRight)
    // {
    //     $operator = $isRight ? '>=' : '<=';

    //     $query = DB::table('blacklist')
    //         ->where('id', $operator, $start) 
    //         ->where('id_blacklist_type', $blacklistType);

    //     $totalElements = $query->count();

    //     $data = $query
    //         ->orderBy('id') 
    //         ->take(20) 
    //         ->get();

    //     return [
    //         'data' => $data, 
    //         'totalElements' => $totalElements, 
    //         'remainingElements' => $totalElements - $data->count()
    //     ];
    // }
}