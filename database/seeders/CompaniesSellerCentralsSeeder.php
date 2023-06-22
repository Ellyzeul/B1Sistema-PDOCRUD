<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSellerCentralsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies_sellercentrals')
            ->insert([
                ['id_company' => 0, 'id_sellercentral' => 6], 
                ['id_company' => 0, 'id_sellercentral' => 8], 
                ['id_company' => 0, 'id_sellercentral' => 9], 
                ['id_company' => 0, 'id_sellercentral' => 10], 
                ['id_company' => 1, 'id_sellercentral' => 4], 
                ['id_company' => 1, 'id_sellercentral' => 9], 
            ]);        
    }
}
