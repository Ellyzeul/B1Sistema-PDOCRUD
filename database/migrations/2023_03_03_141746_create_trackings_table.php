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
        Schema::create('trackings', function (Blueprint $table) {
            $table->string('tracking_code', 45)->primary();
            $table->tinyText('status');
            $table->date('last_update_date');
            $table->longText('details');
            $table->date('delivery_expeted_date');
            $table->longText('observation');
            $table->date('api_calling_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trackings');
    }
};
