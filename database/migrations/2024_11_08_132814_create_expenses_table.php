<?php

use App\Models\Company;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Company::class)->constrained();
            $table->foreignIdFor(ExpenseCategory::class)->constrained();
            $table->string('annotations')->nullable();
            $table->string('supplier')->nullable();
            $table->integer('bank_id');
            $table->foreignIdFor(PaymentMethod::class)->constrained();
            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('value');
            $table->enum('status', ['paid', 'late', 'pending'])->default('pending');
            $table->boolean('has_match')->default(false);
            $table->boolean('on_financial')->default(false);
            $table->string('fiscal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
