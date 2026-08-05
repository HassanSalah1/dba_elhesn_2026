<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sport_games', function (Blueprint $table) {
            $table->text('championships_ar')->nullable()->after('history_en');
            $table->text('championships_en')->nullable()->after('championships_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sport_games', function (Blueprint $table) {
            $table->dropColumn(['championships_ar', 'championships_en']);
        });
    }
};
