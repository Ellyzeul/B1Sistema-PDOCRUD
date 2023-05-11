<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_addresses')
            ->where('id', 1)
            ->update(['name' => 'Coworking', 'postal_code' => '02965050']);

        DB::table('delivery_addresses')
            ->where('id', 2)
            ->update(['name' => 'Caixa Postal', 'postal_code' => '02965050']);

        DB::table('delivery_addresses')
            ->where('id', 3)
            ->update(['name' => 'Itaberaba', 'postal_code' => '02965050']);

        DB::table('delivery_addresses')
            ->where('id', 4)
            ->update(['name' => 'Praça', 'postal_code' => '02965050']);

        DB::table('delivery_addresses')
            ->where('id', 5)
            ->update(['name' => 'Expedição', 'postal_code' => '02965050']);

        DB::table('delivery_addresses')
            ->where('id', 6)
            ->update(['name' => 'Sorocaba', 'postal_code' => '18053525']);

        DB::table('delivery_addresses')
            ->where('id', 7)
            ->update(['name' => 'Parnamirim', 'postal_code' => '59152230']);
    }
}
