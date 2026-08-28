<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncColumnsToOfficialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('administrative_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('administrative_reports', 'row_id')) {
                $table->integer('row_id')->nullable()->index()->after('id');
            }
            if (!Schema::hasColumn('administrative_reports', 'official_id')) {
                $table->integer('official_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('administrative_reports', 'synced_to_sqlserver')) {
                $table->boolean('synced_to_sqlserver')->default(false)->after('location');
            }
        });

        Schema::table('advance_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('advance_requests', 'row_id')) {
                $table->integer('row_id')->nullable()->index()->after('id');
            }
            if (!Schema::hasColumn('advance_requests', 'team_row_id')) {
                $table->integer('team_row_id')->nullable()->after('user_team_id');
            }
            if (!Schema::hasColumn('advance_requests', 'details')) {
                $table->text('details')->nullable()->after('statement');
            }
            if (!Schema::hasColumn('advance_requests', 'type')) {
                $table->string('type')->nullable()->after('details');
            }
            if (!Schema::hasColumn('advance_requests', 'status')) {
                $table->string('status')->default('pending')->after('type');
            }
            if (!Schema::hasColumn('advance_requests', 'synced_to_sqlserver')) {
                $table->boolean('synced_to_sqlserver')->default(false)->after('snacks');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('administrative_reports', function (Blueprint $table) {
            $table->dropColumn(['row_id', 'official_id', 'synced_to_sqlserver']);
        });

        Schema::table('advance_requests', function (Blueprint $table) {
            $table->dropColumn(['row_id', 'team_row_id', 'details', 'type', 'status', 'synced_to_sqlserver']);
        });
    }
}
