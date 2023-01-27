<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('companies')
			->insert([
				[
					'id' => 0,
					'name' => 'Livraria Seline',
					'thumbnail' => '/seline_white_bg.png',
					'company_name' => 'RV de Lima Comercio de Livros Ltda',
					'address' => 'Rua José Luis da Silva Gomes, 102' . PHP_EOL .  '029065-050 São Paulo - SP',
					'cnpj' => '26.779.333/0001-54',
					'state_registration' => '141.692.264.114',
					'municipal_registration' => '5.635.526-2',
				],
				[
					'id' => 1,
					'name' => 'Seline Livros',
					'thumbnail' => '/b1_white_bg.png',
					'company_name' => 'B1 Comercio de Livros e Distribuidora LTDA',
					'address' => 'Rua José Luis da Silva Gomes, 102' . PHP_EOL .  '029065-050 São Paulo - SP',
					'cnpj' => '47.317.204/0001-14',
					'state_registration' => '136.548.985.112',
					'municipal_registration' => '7.401.000-0',
				],
			]);
	}
}
