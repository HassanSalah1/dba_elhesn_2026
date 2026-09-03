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
        Schema::table('advance_requests', function (Blueprint $table) {
            $table->string('team_name')->nullable()->after('team_row_id');
            $table->string('leave_time')->nullable()->after('match_timing');
            $table->integer('breakfast_count')->default(0)->after('return_date');
            $table->decimal('breakfast_cost', 16, 2)->default(0)->after('breakfast_count');
            $table->integer('lunch_count')->default(0)->after('breakfast_cost');
            $table->decimal('lunch_cost', 16, 2)->default(0)->after('lunch_count');
            $table->integer('dinner_count')->default(0)->after('lunch_cost');
            $table->decimal('dinner_cost', 16, 2)->default(0)->after('dinner_count');
            $table->integer('snack_count')->default(0)->after('dinner_cost');
            $table->decimal('snack_cost', 16, 2)->default(0)->after('snack_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advance_requests', function (Blueprint $table) {
            $table->dropColumn([
                'team_name',
                'leave_time',
                'breakfast_count',
                'breakfast_cost',
                'lunch_count',
                'lunch_cost',
                'dinner_count',
                'dinner_cost',
                'snack_count',
                'snack_cost',
            ]);
        });
    }
};
