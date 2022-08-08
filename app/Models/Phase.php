<?php namespace App\Models;

use Illuminate\Support\Facades\DB;

class Phase
{
    public static function read($email)
    {
        $results = DB::select("CALL select_phases_of_user(?)", [
            $email
        ]);
        $items = [];

        foreach($results as $item) {
            $urlParts = explode("=", $item->url);
            $label = isset($urlParts[1]) ? "Fase ".explode(".", $urlParts[1])[0] : "inicio";
            if($label == "inicio") {
                $items[$label] = [[
                    "name" => "Ir para a geral",
                    "url" => $item->url
                ]];
                continue;
            }
            $items[$label] = isset($items[$label]) 
                ? array_merge($items[$label], [$item]) 
                : [$item];
        }

        return [
            "message" => "Fases do processo para a barra de navegação foram recuperadas",
            "items" => $items
        ];
    }
}
