<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NavbarItem extends Model
{
    use HasFactory;

    public function read(string $email)
    {
        $results = DB::select("CALL select_navbar_items_of_user(?)", [
            $email
        ]);
        $items = [];

        foreach($results as $item) {
            $items[$item->label] = isset($items[$item->label]) 
                ? array_merge($items[$item->label], [$item->url])
                : [$item->url];
        }

        $items = $this->treatItems($items);

        return [
            "message" => "Itens para a barra de navegação foram recuperados",
            "items" => $items
        ];
    }

    private function treatItems(array $items)
    {
        if(isset($items["Pedidos"])) $items["Pedidos"] = $this->treatPhases($items["Pedidos"]);

        return $items;
    }

    private function treatPhases(array $phases)
    {
        $treated = ["order" => []];
        $lastOrderKey = null;
        foreach($phases as $phase) {
            $explodedByEqual = explode("=", $phase);
            if(count($explodedByEqual) == 1) $arrKey = "general";
            else $arrKey = explode(".", $explodedByEqual[1])[0];

            $treated[$arrKey] = isset($treated[$arrKey]) 
                ? array_merge($treated[$arrKey], [$phase])
                : [$phase];
            if($lastOrderKey != $arrKey) {
                array_push($treated["order"], $arrKey);
                $lastOrderKey = $arrKey;
            }
        }

        return $treated;
    }
}
