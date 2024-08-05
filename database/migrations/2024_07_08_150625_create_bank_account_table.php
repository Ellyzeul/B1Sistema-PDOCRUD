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
        Schema::create('bank_account', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->tinyInteger('id_company');
            $table->enum('person_type', ['PJ', 'PF']);
            $table->string('bank_code', 3);
            $table->string('bank_name');
            $table->string('bank_agency');
            $table->string('account_number');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

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
        Schema::dropIfExists('bank_account');
    }
};
