<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DeliveryMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_methods')
            ->upsert([
                ['id' => 1, 'name' => 'Motoboy'], 
                ['id' => 2, 'name' => 'Correios'], 
                ['id' => 3, 'name' => 'DHL'], 
                ['id' => 4, 'name' => 'FedEx'], 
                ['id' => 5, 'name' => 'Jadlog'], 
                ['id' => 6, 'name' => 'Outros'],
                ['id' => 7, 'name' => 'Encerrado'], 
                ['id' => 9, 'name' => 'Mercado Livre'], 
            ], 'name');
    }
}
