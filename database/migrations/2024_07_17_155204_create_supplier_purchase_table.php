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
        Schema::create('supplier_purchase', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_supplier')->unsigned();
            $table->enum('purchase_method', ['email', 'site', 'phone', 'whatsapp']);
            $table->tinyInteger('id_company');
            $table->decimal('freight')->default(0);
            $table->decimal('sales_total');

            $table->foreign('id_supplier')->on('supplier')->references('id');
            $table->foreign('id_company')->on('companies')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_purchase');
    }
};
