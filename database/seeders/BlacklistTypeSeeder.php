<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlacklistTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklist_type')
            ->insert([
                ['id' => 1, 'name' => 'ISBN'], 
                ['id' => 2, 'name' => 'Fornecedor'], 
                ['id' => 3, 'name' => 'Editora'], 
            ]);
    }
}
