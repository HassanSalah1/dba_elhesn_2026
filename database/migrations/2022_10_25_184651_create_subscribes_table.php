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
            $table->string('player_full_name_ar');
            $table->string('player_full_name_en');
            $table->string('player_photo')->nullable();
            $table->date('birth_date');
            $table->string('nationality');
            $table->string('birth_place');
            $table->string('parent_full_name_ar');
            $table->string('parent_full_name_en');
            $table->string('parent_email');
            $table->tinyInteger('parent_category');
            $table->string('parent_passport_photo')->nullable();
            $table->string('parent_residence_photo')->nullable();
            $table->string('parent_id_photo')->nullable();
            $table->string('clothes_size');
            $table->string('shoe_size');
            $table->string('weight');
            $table->string('height');
            $table->tinyInteger('is_another_club');
            $table->string('another_club_name')->nullable();
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
