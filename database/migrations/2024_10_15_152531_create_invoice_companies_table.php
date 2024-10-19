<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CPNJ_SIZE = 18;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_companies', function (Blueprint $table) {
            $table->char('cnpj', self::CPNJ_SIZE)->primary();
            $table->string('name')->nullable();
            $table->string('ie')->nullable();
            $table->string('uf')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_companies');
    }
};
