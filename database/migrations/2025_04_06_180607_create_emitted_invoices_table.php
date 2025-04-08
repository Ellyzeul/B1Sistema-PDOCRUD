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
        Schema::create('emitted_invoices', function (Blueprint $table) {
            $table->char('key', 44)->primary();
            $table->integer('number')->unsigned()->index();
            $table->dateTime('emitted_at')->nullable();
            $table->string('order_number')->nullable()->index();
            $table->enum('company', ['seline', 'b1']);
            $table->tinyText('link_danfe')->nullable();
            $table->tinyText('link_xml')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->boolean('cancelment_same_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emitted_invoices');
    }
};
