<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inventory_location')
            ->insert([
                ['location' => 'São Paulo'], 
                ['location' => 'Sorocaba'], 
                ['location' => 'Natal'], 
            ]);
    }
}
