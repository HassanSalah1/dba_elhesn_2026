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
        Schema::create('clinic_time_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('day_of_week');          // Saturday, Sunday, etc.
            $table->time('start_time');              // 09:00
            $table->time('end_time');                // 09:30
            $table->integer('max_bookings')->default(1); // الحد الأقصى للحجوزات في نفس الموعد
            $table->tinyInteger('status')->default(1);   // 0=inactive, 1=active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_time_slots');
    }
};
