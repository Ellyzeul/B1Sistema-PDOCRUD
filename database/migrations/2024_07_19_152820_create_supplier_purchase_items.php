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
        Schema::create('supplier_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_purchase')->unsigned();
            $table->bigInteger('id_order')->unsigned();
            $table->decimal('value');

            $table->foreign('id_purchase')->on('supplier_purchase')->references('id');
            $table->foreign('id_order')->on('order_control')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_purchase_items');
    }
};
