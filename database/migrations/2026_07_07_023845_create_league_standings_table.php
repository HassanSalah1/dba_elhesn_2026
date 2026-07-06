<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeagueStandingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_standings', function (Blueprint $table) {
            $table->id();
            $table->integer('competition_row_id')->index();
            $table->integer('club_id')->nullable()->index();
            $table->string('team_name')->nullable();
            $table->integer('play')->default(0);
            $table->integer('win')->default(0);
            $table->integer('draw')->default(0);
            $table->integer('lose')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goals_diff')->default(0);
            $table->integer('points')->default(0);
            $table->integer('rank')->default(0);
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
        Schema::dropIfExists('league_standings');
    }
}
