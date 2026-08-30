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
        Schema::table('hr_documents', function (Blueprint $table) {
            $table->unsignedTinyInteger('status_id')->default(1)->after('attachment_path'); // 1: Waiting for Approval, 2: Approved, 3: Rejected
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_documents', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
};
