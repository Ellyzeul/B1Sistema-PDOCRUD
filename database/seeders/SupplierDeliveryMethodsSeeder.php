<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierDeliveryMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('supplier_delivery_methods')
            ->insert([
                ['id' => 1, 'name' => 'Correios'], 
                ['id' => 2, 'name' => 'Correios Reverso'], 
                ['id' => 3, 'name' => 'Jadlog'], 
                ['id' => 4, 'name' => 'Loggi'], 
                ['id' => 5, 'name' => 'DHL'], 
                ['id' => 6, 'name' => 'Outras'], 
            ]);
    }
}
