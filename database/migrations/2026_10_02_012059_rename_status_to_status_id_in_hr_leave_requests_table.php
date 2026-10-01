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
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->renameColumn('status', 'status_id');
        });

        // Use raw query or another schema modification to change the default value and comment if necessary.
        // Doing this separately avoids issues in some MySQL/MariaDB versions.
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->tinyInteger('status_id')->default(1)->comment('1:Waiting for Approval, 2:Approved, 3:Rejected')->change();
        });
        
        // Also update existing data: previous 0 (Pending) -> 1, previous 1 (Approved) -> 2, previous 2 (Rejected) -> 3
        \Illuminate\Support\Facades\DB::table('hr_leave_requests')->where('status_id', 2)->update(['status_id' => 3]);
        \Illuminate\Support\Facades\DB::table('hr_leave_requests')->where('status_id', 1)->update(['status_id' => 2]);
        \Illuminate\Support\Facades\DB::table('hr_leave_requests')->where('status_id', 0)->update(['status_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->tinyInteger('status_id')->default(0)->comment('0:Pending, 1:Approved, 2:Rejected')->change();
        });
        
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->renameColumn('status_id', 'status');
        });
    }
};
