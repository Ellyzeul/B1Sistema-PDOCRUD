<?php

use App\Models\InvoiceCompany;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INVOICE_KEY_SIZE = 44;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->char('key', self::INVOICE_KEY_SIZE)->primary();
            $table->timestamp('emitted_at');
            $table->char('period', 7);
            $table->enum('type', ['in', 'out']);
            $table->decimal('value', 9, 2)->unsigned();
            $table->enum('status', ['authorized', 'cancelled']);
            $table->enum('manifestation', ['confirmed', 'acknowledged'])->nullable();
            $table->foreignIdFor(InvoiceCompany::class, 'emitter_cnpj')->nullable();
            $table->foreignIdFor(InvoiceCompany::class, 'recipient_cnpj')->nullable();
            $table->foreignIdFor(InvoiceCompany::class, 'courier_cnpj')->nullable();
            $table->boolean('has_xml');
            $table->string('origin')->nullable();
            $table->string('cfops')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
