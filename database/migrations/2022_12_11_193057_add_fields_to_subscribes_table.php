<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSubscribesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribes', function (Blueprint $table) {
            //
            $table->integer('user_id')->nullable();
            $table->string('player_id_number');
            $table->date('player_id_expire');
            $table->string('player_passport_number');
            $table->date('player_passport_expire');
            $table->string('player_birth_certificate_photo')->nullable();
            $table->string('player_phone')->nullable();
            $table->string('player_school_name')->nullable();
            $table->string('player_class_name')->nullable();
            $table->string('sport_level');
            $table->string('vaccine_count');

            $table->date('vaccine_1')->nullable();
            $table->date('vaccine_2')->nullable();
            $table->date('vaccine_3')->nullable();
            $table->string('player_passport_photo');
            $table->string('player_medical_examination_photo');
            $table->string('player_mother_passport_photo')->nullable();
            $table->string('player_kafel_passport_photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribes', function (Blueprint $table) {
            //
        });
    }
}
