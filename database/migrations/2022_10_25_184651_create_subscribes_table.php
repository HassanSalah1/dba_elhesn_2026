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
            $table->integer('user_id')->nullable();

            $table->string('player_email');
            $table->string('player_full_name_ar');
            $table->string('player_full_name_en');
            $table->date('birth_date');
            $table->string('nationality');
            $table->string('birth_place');
            $table->string('player_category');
            $table->string('player_id_number');
            $table->date('player_id_expire_date');
            $table->string('player_passport_number');
            $table->date('player_passport_expire_date');
            $table->string('address');
            $table->string('player_phone')->nullable();
            $table->string('player_school_name')->nullable();
            $table->string('player_class_name')->nullable();
            $table->string('another_club_name')->nullable();
            $table->integer('sport_id');
            $table->string('sport_level');
            $table->string('weight');
            $table->string('height');
            $table->string('clothes_size');
            $table->string('shoe_size');
            $table->string('parent_phone');
            $table->string('parent_job')->nullable();
            $table->string('player_photo');
            $table->string('player_id_photo');
            $table->string('player_passport_photo');
            $table->string('player_medical_examination_photo');
            $table->string('player_birth_certificate_photo')->nullable();
            $table->string('parent_id_photo');
            $table->string('parent_passport_photo')->nullable();
            $table->string('parent_residence_photo')->nullable();
            $table->string('parent_acknowledgment_file')->nullable();
            $table->string('player_mother_passport_photo')->nullable();
            $table->string('player_kafel_passport_photo')->nullable();
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
