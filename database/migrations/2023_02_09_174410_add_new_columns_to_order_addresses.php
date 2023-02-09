<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('buyer_name', 120)->nullable();
            $table->string('expected_date', 10)->nullable();
            $table->decimal('price', $precision = 8, $scale = 2)->nullable();
            $table->decimal('freight', $precision = 5, $scale = 2)->nullable();
            $table->decimal('item_tax', $precision = 4, $scale = 2)->nullable();
            $table->decimal('freight_tax', $precision = 4, $scale = 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            //
        });
    }
};
