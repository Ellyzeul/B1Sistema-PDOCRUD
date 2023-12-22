<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderTicketSituationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_ticket_situation')
            ->insert([
                ['name' => 'Pendente'], 
                ['name' => 'Respondido previamente'], 
                ['name' => 'Respondido'], 
                ['name' => 'Finalizado'], 
            ]);
    }
}
