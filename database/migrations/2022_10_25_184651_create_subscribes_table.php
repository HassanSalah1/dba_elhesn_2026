<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('full_name_ar');
            $table->string('full_name_en');
            $table->date('birth_date');
            $table->string('nationality');
            $table->string('birth_place');
            $table->string('category');
            $table->string('identification_number');
            $table->date('id_expiration_date');
            $table->string('passport_number');
            $table->date('passport_expiry');
            $table->string('address');
            $table->string('guardian_phone');
            $table->string('another_club');
            $table->integer('sport_id');
            $table->integer('level');
            $table->string('weight');
            $table->string('height');
            $table->string('clothes_size');
            $table->string('shoe_size');
            $table->string('vaccine_number');
            $table->string('school')->nullable();
            $table->string('guardian_job')->nullable();
            $table->string('first_dose_date')->nullable();
            $table->string('second_dose_date')->nullable();
            $table->string('third_dose_date')->nullable();
            $table->string('personal_image');
            $table->string('id_photo');
            $table->string('player_passport_photo');
            $table->string('parent_id_photo');
            $table->string('parent_passport_photo');
            $table->string('player_parent_residence_photo');
            $table->string('medical_examination_photo');
            $table->string('parent_acknowledgment_photo')->nullable();
            $table->string('player_birth_certificate_photo')->nullable();
            $table->string('player_mother_copy_photo')->nullable();
            $table->string('sponsor_residence_photo')->nullable();
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
        Schema::dropIfExists('subscribes');
    }
}
