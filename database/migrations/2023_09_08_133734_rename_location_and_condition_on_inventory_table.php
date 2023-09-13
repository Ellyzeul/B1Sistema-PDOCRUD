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
        // Schema::table('inventory', function (Blueprint $table) {
        //     $table->dropForeign(['location']);
        //     $table->dropForeign(['condition']);
        
        //     $table->renameColumn('location', 'id_location');
        //     $table->renameColumn('condition', 'id_condition');
        // });
        
        Schema::table('inventory', function (Blueprint $table) {
            $table->unsignedTinyInteger('id_location')->change();
            $table->unsignedTinyInteger('id_condition')->change();
        
            $table->foreign('id_condition')->references('id')->on('inventory_condition');
            $table->foreign('id_location')->references('id')->on('inventory_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
