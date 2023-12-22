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
        Schema::create('order_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('id_type');
            $table->string('online_order_number', 45)->unique();
            $table->unsignedTinyInteger('has_attachments')->nullable();
            $table->unsignedTinyInteger('id_company');
            $table->unsignedTinyInteger('id_sellercentral');
            $table->text('observation')->nullable();
            $table->unsignedTinyInteger('id_situation');
            $table->datetime('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_tickets');
    }
};
