<?php

use App\Models\Expense;
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
        Schema::create('expense_documents', function (Blueprint $table) {
            $table->string('key', 44)->primary();
            $table->date('created_at');
            $table->enum('type', ['recibo', 'fatura', 'boleto', 'nfs', 'cupom', 'outros']);
            $table->string('issuer')->nullable();
            $table->decimal('value');
            $table->string('filename')->nullable();
            $table->foreignIdFor(Expense::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_documents');
    }
};
