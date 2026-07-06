<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdsToSportMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sport_matches', function (Blueprint $table) {
            $table->integer('team1_row_id')->nullable()->after('team1');
            $table->integer('team2_row_id')->nullable()->after('team2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sport_matches', function (Blueprint $table) {
            $table->dropColumn(['team1_row_id', 'team2_row_id']);
        });
    }
}
