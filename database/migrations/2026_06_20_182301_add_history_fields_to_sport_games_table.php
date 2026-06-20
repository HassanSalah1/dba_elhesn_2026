<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHistoryFieldsToSportGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sport_games', function (Blueprint $table) {
            $table->text('history_ar')->nullable()->after('description_en');
            $table->text('history_en')->nullable()->after('history_ar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sport_games', function (Blueprint $table) {
            $table->dropColumn(['history_ar', 'history_en']);
        });
    }
}
