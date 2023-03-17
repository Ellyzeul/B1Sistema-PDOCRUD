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
        Schema::table('order_control', function (Blueprint $table) {
            $table->string('supplier_tracking_code', 45)->nullable();
            $table->tinyInteger('id_supplier_delivery_method')->nullable();
            $table->date('ship_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_control', function (Blueprint $table) {
            //
        });
    }
};
