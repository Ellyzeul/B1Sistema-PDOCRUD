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
		Schema::create('companies_accounts', function (Blueprint $table) {
			$table->tinyInteger('id_company');
			$table->integer('id_bank');
			$table->string('name', 100);
			$table->string('account', 45);
			$table->string('agency', 45);

			$table->foreignId('id_company')
				->references('companies')
				->on('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('companies_accounts');
	}
};
