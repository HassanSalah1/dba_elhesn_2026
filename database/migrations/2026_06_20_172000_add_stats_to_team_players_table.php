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
        Schema::table('team_players', function (Blueprint $table) {
            $table->string('position_ar')->nullable()->after('name_en');
            $table->string('position_en')->nullable()->after('position_ar');
            
            $table->integer('goals')->default(0)->after('weight');
            $table->integer('wins')->default(0)->after('goals');
            $table->integer('losses')->default(0)->after('wins');
            
            $table->integer('matches_played')->default(0)->after('losses');
            $table->integer('minutes_played')->default(0)->after('matches_played');
            
            $table->integer('yellow_cards')->default(0)->after('minutes_played');
            $table->integer('red_cards')->default(0)->after('yellow_cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_players', function (Blueprint $table) {
            $table->dropColumn([
                'position_ar', 'position_en', 'goals', 'wins', 'losses',
                'matches_played', 'minutes_played', 'yellow_cards', 'red_cards'
            ]);
        });
    }
};
