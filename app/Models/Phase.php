<?php namespace App\Models;

use Illuminate\Support\Facades\DB;

class Phase
{
    private static array $generalOptions = [
        "general" => "Ir para a geral", 
        "não-verificado" => "Endereços a arrumar", 
        "não-enviado" => "Em prepraração para envio", 
        "aceitar-fnac" => "Aceitar FNAC", 
        "cancelar-nf" => "Cancelar Notas Fiscais (Fase 8)", 
    ];
    public static function read($email)
    {
        $results = DB::select("CALL select_phases_of_user(?)", [
            $email
        ]);
        $items = [];

        foreach($results as $item) {
            $urlParts = explode("=", $item->url);
            $label = isset($urlParts[1]) && \is_numeric($urlParts[1]) 
                ? "Fase ".explode(".", $urlParts[1])[0] 
                : "inicio";
            if($label == "inicio") {
                $toAppend = [[
                    "name" => Phase::$generalOptions[$urlParts[1] ?? "general"], 
                    "url" => $item->url
                ]];
                $items[$label] = isset($items[$label])
                ? array_merge($items[$label], $toAppend)
                : $toAppend;
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
