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
        Schema::create('purchase_trackings', function (Blueprint $table) {
            $table->string('tracking_code', 45);
            $table->tinyText('status')->nullable();
            $table->date('last_update_date')->nullable();
            $table->text('details')->nullable();
            $table->date('delivery_expected_date')->nullable();
            $table->text('observation')->nullable();
            $table->date('api_calling_date')->nullable();
            $table->date('deadline')->nullable();
        });

        DB::unprepared(
            'CREATE TRIGGER update_purchase_trackings_BEFORE_UPDATE BEFORE UPDATE ON order_control FOR EACH ROW
                BEGIN
                   IF (NEW.id_phase = "3.1" AND LENGTH(NEW.supplier_tracking_code) > 0) THEN
                        INSERT INTO purchase_trackings(
                            tracking_code
                        ) VALUES(NEW.supplier_tracking_code) ON DUPLICATE KEY UPDATE tracking_code = NEW.supplier_tracking_code;
                   END IF;
                END
        ');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `update_purchase_trackings_BEFORE_INSERT`');
        Schema::dropIfExists('purchase_trackings');
    }
};
