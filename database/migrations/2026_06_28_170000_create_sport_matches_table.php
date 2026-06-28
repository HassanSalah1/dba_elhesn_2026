<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSportMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sport_matches', function (Blueprint $table) {
            $table->id();
            $table->integer('row_id')->unique();
            $table->integer('season_row_id')->nullable();
            $table->integer('competition_row_id')->nullable();
            $table->string('team1', 255)->nullable();
            $table->string('team2', 255)->nullable();
            $table->date('match_date')->nullable();
            $table->string('match_time', 255)->nullable();
            $table->string('stage_round', 255)->nullable();
            $table->integer('match_number')->nullable();
            $table->integer('week')->nullable();
            $table->string('pitch', 255)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->integer('team1_result')->nullable();
            $table->integer('team2_result')->nullable();
            $table->string('match_in_house', 255)->nullable();
            $table->integer('fanet_match_id')->nullable();
            $table->text('live_link')->nullable();
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
        Schema::dropIfExists('sport_matches');
    }
}
