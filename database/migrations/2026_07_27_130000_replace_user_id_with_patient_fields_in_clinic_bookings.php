<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clinic_bookings', function (Blueprint $table) {
            // Drop foreign key first, then column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Add patient fields
            $table->string('patient_name')->nullable()->after('id');
            $table->string('patient_phone')->nullable()->after('patient_name');
        });
    }

    public function down()
    {
        Schema::table('clinic_bookings', function (Blueprint $table) {
            $table->dropColumn(['patient_name', 'patient_phone']);
            $table->unsignedInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
