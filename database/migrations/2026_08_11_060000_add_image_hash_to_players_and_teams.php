<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_players', function (Blueprint $table) {
            $table->string('image_hash', 32)->nullable()->after('image')
                ->comment('MD5 hash of the SQL Server photo binary for change detection');
        });

        Schema::table('sport_teams', function (Blueprint $table) {
            $table->string('image_hash', 32)->nullable()->after('image')
                ->comment('MD5 hash of the SQL Server photo binary for change detection');
        });
    }

    public function down(): void
    {
        Schema::table('team_players', function (Blueprint $table) {
            $table->dropColumn('image_hash');
        });

        Schema::table('sport_teams', function (Blueprint $table) {
            $table->dropColumn('image_hash');
        });
    }
};
