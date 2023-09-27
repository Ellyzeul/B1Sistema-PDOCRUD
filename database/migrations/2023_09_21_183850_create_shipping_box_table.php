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
        Schema::create('shipping_box', function (Blueprint $table) {
            $table->string('id', 45);
            $table->string('delivery_hub')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('url')->nullable();
            $table->date('expected_date')->nullable();
            $table->date('courier_delivered_date')->nullable();
            $table->unsignedInteger('items')->default(0);
            $table->decimal('weight', 5, 2)->default(0);
            $table->decimal('total_cost')->nullable();
            $table->unsignedTinyInteger('delivered_on_envia_com')->default(0);
            $table->date('hub_ship_date')->nullable();
            $table->unsignedTinyInteger('status')->nullable();

            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_box');
    }
};
