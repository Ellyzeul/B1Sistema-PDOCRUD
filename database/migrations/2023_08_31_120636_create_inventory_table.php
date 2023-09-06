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
        Schema::create('inventory', function (Blueprint $table) {
            $table->string('isbn', 13);
            $table->mediumInteger('quantity');
            $table->unsignedTinyInteger('condition');
            $table->unsignedTinyInteger('location');
            $table->mediumInteger('bookshelf')->nullable();
            $table->text('observation')->nullable();
            
            $table->foreign('condition')->references('id')->on('inventory_condition');
            $table->foreign('location')->references('id')->on('inventory_location');
            $table->primary(['isbn', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};