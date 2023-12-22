<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_ticket_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_order_ticket')->nullable();
            $table->string('online_message_number', 45)->unique();
            $table->text('message');
            $table->datetime('timestamp');
            $table->unsignedTinyInteger('is_client_message');
        });

        Schema::table('order_ticket_messages', function (Blueprint $table) {
            DB::unprepared("
                CREATE TRIGGER insert_id_situation_into_order_tickets_after_insert
                AFTER INSERT ON order_ticket_messages
                FOR EACH ROW
                BEGIN
                    IF NEW.is_client_message = 0 THEN
                        UPDATE order_tickets
                        SET id_situation = 2
                        WHERE id = NEW.id_order_ticket;
                    END IF;
                END;
            ");
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_ticket_messages');
    }
};
