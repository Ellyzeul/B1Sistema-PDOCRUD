<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
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
            $table->date('date')->nullable();
            $table->enum('status', ['normal', 'cancelled', 'cancelled_partial', 'multiple_delivery'])->default('normal');
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
