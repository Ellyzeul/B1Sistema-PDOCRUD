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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('thumbnail', 255)->nullable();
            $table->string('company_name', 120)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('cnpj', 45)->nullable();
            $table->string('state_registration', 45)->nullable();
            $table->string('municipal_registration', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
