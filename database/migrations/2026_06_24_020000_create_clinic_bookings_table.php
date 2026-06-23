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
        Schema::create('clinic_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');           // المستخدم اللي عمل الحجز
            $table->unsignedInteger('time_slot_id');       // الموعد المختار
            $table->date('booking_date');                  // تاريخ الحجز
            $table->tinyInteger('is_for_other')->default(0); // هل الحجز لشخص آخر
            $table->string('other_name')->nullable();      // اسم الشخص الآخر
            $table->string('other_phone')->nullable();     // رقم جوال الشخص الآخر
            $table->string('other_country_code')->nullable(); // كود الدولة
            $table->text('injury_type')->nullable();       // نوع/تفاصيل الإصابة
            $table->text('description')->nullable();       // الوصف
            $table->string('status')->default('pending');  // pending, confirmed, completed, cancelled
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('time_slot_id')->references('id')->on('clinic_time_slots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_bookings');
    }
};
