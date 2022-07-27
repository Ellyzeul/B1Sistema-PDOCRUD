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
            $toAppend = [$item->url];
            $items[$item->label] = isset($items[$item->label]) 
                ? array_merge($items[$item->label], $toAppend)
                : $toAppend;
        }

        return [
            "message" => "Itens para a barra de navegação foram recuperados",
            "items" => $items
        ];
    }
}
