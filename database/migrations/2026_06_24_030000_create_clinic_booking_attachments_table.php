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
        Schema::create('clinic_booking_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('booking_id');
            $table->string('file_name');          // اسم الملف الأصلي
            $table->string('file_path');          // المسار على السيرفر
            $table->string('file_type');          // pdf, jpeg, png
            $table->bigInteger('file_size');      // الحجم بالبايت
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('clinic_bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinic_booking_attachments');
    }
};
