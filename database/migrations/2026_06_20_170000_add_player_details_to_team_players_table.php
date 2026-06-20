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
            $table->date('birth_date')->nullable()->after('number');
            $table->string('nationality_ar')->nullable()->after('birth_date');
            $table->string('nationality_en')->nullable()->after('nationality_ar');
            $table->integer('height')->nullable()->after('nationality_en'); // cm
            $table->integer('weight')->nullable()->after('height'); // kg
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
            $table->dropColumn(['birth_date', 'nationality_ar', 'nationality_en', 'height', 'weight']);
        });
    }
};
