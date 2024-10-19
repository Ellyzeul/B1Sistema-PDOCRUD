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
        Schema::table('supplier_purchase', function (Blueprint $table) {
            $table->tinyInteger('id_delivery_address')->nullable();
            $table->string('order_number')->nullable();
            $table->string('tracking_code')->nullable();
            $table->tinyInteger('id_delivery_method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_purchase', function (Blueprint $table) {
            //
        });
    }
};
