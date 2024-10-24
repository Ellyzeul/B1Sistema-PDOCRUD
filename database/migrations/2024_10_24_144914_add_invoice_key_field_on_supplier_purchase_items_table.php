<?php

use App\Models\Invoice;
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
        Schema::table('supplier_purchase_items', function (Blueprint $table) {
            $table->char('invoice_key', 44)->nullable();

            $table->foreign('invoice_key')->on('invoices')->references('key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_purchase_items', function (Blueprint $table) {
            //
        });
    }
};
