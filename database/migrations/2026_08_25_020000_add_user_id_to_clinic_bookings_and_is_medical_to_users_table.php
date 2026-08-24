<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('clinic_bookings', 'user_id')) {
            Schema::table('clinic_bookings', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('users', 'is_medical')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_medical')->default(false)->after('status');
            });
        }

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','fan','employee','Official','CoachGK','CoachGKJunior','Coach','Foot','Medical') NOT NULL DEFAULT 'fan'");
        } catch (\Exception $e) {
            // Ignore if already modified or SQLite in test
        }
    }

    public function down()
    {
        if (Schema::hasColumn('clinic_bookings', 'user_id')) {
            Schema::table('clinic_bookings', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('users', 'is_medical')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_medical');
            });
        }
    }
};
