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
        Schema::table('teams', function (Blueprint $table) {
            $table->renameColumn('title', 'title_ar');
            $table->renameColumn('name', 'name_ar');
            $table->renameColumn('position', 'position_ar');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title_ar');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->string('position_en')->nullable()->after('position_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'name_en', 'position_en']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->renameColumn('title_ar', 'title');
            $table->renameColumn('name_ar', 'name');
            $table->renameColumn('position_ar', 'position');
        });
    }
};
