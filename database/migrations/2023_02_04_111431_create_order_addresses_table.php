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
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->string('online_order_number', 120);
            $table->string('recipient_name', 120)->nullable();
            $table->string('address_1', 255)->nullable();
            $table->string('address_2', 255)->nullable();
            $table->string('address_3', 255)->nullable();
            $table->string('county', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('postal_code', 45)->nullable();
            $table->string('country', 20)->nullable();

            $table->primary('online_order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_addresses');
    }
};
