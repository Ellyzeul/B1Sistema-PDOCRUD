<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('phases')
            ->upsert([
                ['id' => 'Pré-0', 'name' => 'Validação do pedido', 'color' => 'FFFFFF'], 
                ['id' => '0.0', 'name' => 'Novo Pedido', 'color' => 'FFFFFF'], 
                ['id' => '1.1', 'name' => 'Pesquisando Novos', 'color' => 'FFFFFF'], 
                ['id' => '1.2', 'name' => 'Raspagem / EQUIPE', 'color' => 'FF66FF'], 
                ['id' => '1.3', 'name' => 'Raspagem / CHEFIA', 'color' => 'FF66FF'], 
                ['id' => '1.4', 'name' => 'Aguardando Janina', 'color' => 'FFFFFF'], 
                ['id' => '1.5', 'name' => 'Consultar Janina', 'color' => 'FFFFFF'], 
                ['id' => '2.0', 'name' => 'Reservado na loja (Sorocaba)', 'color' => 'FFE699'], 
                ['id' => '2.1', 'name' => 'Comprar Online (Web)', 'color' => 'FFFF66'], 
                ['id' => '2.11', 'name' => 'Reservado transferência', 'color' => 'FFFF66'], 
                ['id' => '2.2', 'name' => 'Reservado na loja (São Paulo)', 'color' => 'FFFF66'], 
                ['id' => '2.3', 'name' => 'Comprado Online(Web)', 'color' => 'FFFF00'], 
                ['id' => '2.4', 'name' => 'Estoque / Expedição', 'color' => 'BF8F00'], 
                ['id' => '2.5', 'name' => 'Estoque / Praça', 'color' => 'BF8F00'], 
                ['id' => '2.6', 'name' => 'Estoque / Sorocaba', 'color' => 'BF8F00'], 
                ['id' => '2.7', 'name' => 'Estoque em Reforma', 'color' => 'BF8F00'], 
                ['id' => '2.8', 'name' => 'Estoque / Parnamirim', 'color' => 'BF8F00'], 
                ['id' => '2.9', 'name' => 'Estoque / Expedição', 'color' => 'BF8F00'], 
                ['id' => '3.1', 'name' => 'Compra Online / Postada', 'color' => 'FFE699'], 
                ['id' => '3.2', 'name' => 'Compra Online / Reclamar', 'color' => 'FFE699'], 
                ['id' => '4.1', 'name' => 'Pré Hub US/Phoenixville (Export)', 'color' => '00CC99'], 
                ['id' => '4.11', 'name' => 'Rota Hub US/Phoenixville (Export)', 'color' => '009999'], 
                ['id' => '4.2', 'name' => 'Rota Hub ES/Madrid (Export)', 'color' => '00CC99'], 
                ['id' => '4.21', 'name' => 'Rota Hub ES/Madrid (Export)', 'color' => '009999'], 
                ['id' => '5.1', 'name' => 'Expedição / Despachado', 'color' => '9BC2E6'], 
                ['id' => '5.2', 'name' => 'Expedição / Reclamar', 'color' => '9BC2E6'], 
                ['id' => '5.3', 'name' => 'Retornando dos Correios', 'color' => '2F75B5'], 
                ['id' => '5.4', 'name' => 'Devolução do Cliente Pendente', 'color' => '2F75B5'], 
                ['id' => '5.5', 'name' => 'Reembolso Correios (Perdido)', 'color' => '2F75B5'], 
                ['id' => '6.1', 'name' => 'Cliente / Entregue', 'color' => 'C6E0B4'], 
                ['id' => '6.2', 'name' => 'Cliente / Pedir Avaliação', 'color' => 'C6E0B4'], 
                ['id' => '6.21', 'name' => 'Cliente / Segunda Avaliação', 'color' => 'C6E0B4'], 
                ['id' => '7.0', 'name' => 'Encerrado / Arquivado', 'color' => '375623'], 
                ['id' => '8.1', 'name' => 'Cancelar', 'color' => 'FF0000'], 
                ['id' => '8.12', 'name' => 'Pré-Cancelamento', 'color' => 'FF0000'], 
                ['id' => '8.13', 'name' => 'Cancelado / Nós cancelamos antes do envio', 'color' => 'FF0000'], 
                ['id' => '8.2', 'name' => 'Cancelado / Devolvido pelos Correios', 'color' => 'FF0000'], 
                ['id' => '8.3', 'name' => 'Cancelado / Nós Cancelamos', 'color' => 'FF0000'], 
                ['id' => '8.4', 'name' => 'Cancelado / Pelo Cliente', 'color' => 'FF0000'], 
                ['id' => '8.5', 'name' => 'Cancelado / Devolvido pelo Cliente', 'color' => 'FF0000'], 
                ['id' => '8.6', 'name' => 'Cancelado / Extraviado pelos Correios', 'color' => 'FF0000'], 
            ], ['id'], ['name', 'color']);
    }
}
