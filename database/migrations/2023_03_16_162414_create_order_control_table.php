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
        Schema::create('order_control', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->autoIncrement();
            $table->tinyInteger('id_company');
            $table->tinyInteger('id_sellercentral');
            $table->string('id_phase', 10)->default('0.0');
            $table->string('invoice_number', 45)->nullable();
            $table->tinyText('online_order_number');
            $table->string('bling_number', 45)->nullable();
			$table->date('order_date');
			$table->date('expected_date');
			$table->char('isbn', 13);
			$table->decimal('selling_price', 8,2);
            $table->tinyText('supplier_name')->nullable();
            $table->date('purchase_date')->nullable();
            $table->tinyInteger('id_delivery_address')->nullable();
            $table->tinyText('supplier_purchase_number')->nullable();
            $table->tinyInteger('id_delivery_method')->nullable();
            $table->string('tracking_code', 45)->nullable();
            $table->tinyText('collection_code')->nullable();
            $table->date('delivered_date')->nullable();
            $table->tinyInteger('ask_rating')->nullable();
            $table->tinyInteger('address_verified')->default('0');
            $table->string('ready_to_6_2', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_control');
    }
};
