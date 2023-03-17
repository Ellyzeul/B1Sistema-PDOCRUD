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
                ['name' => 'Correios'], 
                ['name' => 'Correios Reverso'], 
                ['name' => 'Jadlog'], 
                ['name' => 'Loggi'], 
                ['name' => 'DHL'],
                ['name' => 'Outras']
            ]);
    }
}
