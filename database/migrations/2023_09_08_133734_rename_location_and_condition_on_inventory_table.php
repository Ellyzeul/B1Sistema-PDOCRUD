<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['location']);
            $table->dropForeign(['condition']);

            DB::statement(
                'ALTER TABLE inventory CHANGE COLUMN location id_location TINYINT UNSIGNED NOT NULL'
            );
            DB::statement(
                'ALTER TABLE inventory CHANGE COLUMN `condition` `id_condition` TINYINT UNSIGNED NOT NULL'
            );
        
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
