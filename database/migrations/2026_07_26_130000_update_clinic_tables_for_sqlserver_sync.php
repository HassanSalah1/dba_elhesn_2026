<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add row_id to clinic_time_slots for SQL Server sync
        Schema::table('clinic_time_slots', function (Blueprint $table) {
            $table->unsignedInteger('row_id')->nullable()->unique()->after('id');
        });

        // 2. Remove injury_type from clinic_bookings + add synced flag
        Schema::table('clinic_bookings', function (Blueprint $table) {
            $table->dropColumn('injury_type');
            $table->boolean('synced_to_sqlserver')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('clinic_time_slots', function (Blueprint $table) {
            $table->dropColumn('row_id');
        });

        Schema::table('clinic_bookings', function (Blueprint $table) {
            $table->text('injury_type')->nullable()->after('other_country_code');
            $table->dropColumn('synced_to_sqlserver');
        });
    }
};
