<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellercentralsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sellercentrals')
            ->insert([
                ['id' => 1, 'name' => 'Amazon-BR', 'currency' => 'R$', 'sku_prefix' => 'SEL'], 
                ['id' => 2, 'name' => 'Amazon-CA', 'currency' => 'CA$', 'sku_prefix' => 'CANEXP'], 
                ['id' => 3, 'name' => 'Amazon-UK', 'currency' => '£', 'sku_prefix' => 'EXPORT'], 
                ['id' => 4, 'name' => 'Amazon-US', 'currency' => 'US$', 'sku_prefix' => 'EXPORT'], 
                ['id' => 5, 'name' => 'Seline-BR', 'currency' => 'R$', 'sku_prefix' => 'SEL'], 
                ['id' => 6, 'name' => 'Estante-BR', 'currency' => 'R$', 'sku_prefix' => 'SEL'], 
                ['id' => 7, 'name' => 'Alibris-US', 'currency' => 'US$', 'sku_prefix' => 'SEL'], 
                ['id' => 8, 'name' => 'FNAC-PT', 'currency' => '€', 'sku_prefix' => 'SEL'], 
                ['id' => 9, 'name' => 'MercadoLivre-BR', 'currency' => 'R$', 'sku_prefix' => 'SEL'], 
                ['id' => 10, 'name' => 'MagazineLuiza-BR', 'currency' => 'R$', 'sku_prefix' =>'SEL'], 
                ['id' => 11, 'name' => 'FNAC-ES', 'currency' => '€', 'sku_prefix' =>'SEL'], 
            ]);
    }
}
