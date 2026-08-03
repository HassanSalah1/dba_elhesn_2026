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
        // 1. Employee Categories
        Schema::create('hr_employee_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('row_id')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 2. HR Employees
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->integer('row_id')->unique(); // SQL Server EmployeeRowID
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Linked local user
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('job_title')->nullable();
            $table->string('photo')->nullable(); // Local relative path or URL
            $table->string('username')->unique();
            $table->string('password_hash');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('hr_employee_categories')->onDelete('set null');
        });

        // 3. Attendance Records
        Schema::create('hr_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('row_id')->unique();
            $table->integer('employee_row_id')->index();
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->tinyInteger('status')->comment('1:Present, 2:Absent, 3:Leave, 4:Holiday');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Leave Types
        Schema::create('hr_leave_types', function (Blueprint $table) {
            $table->id();
            $table->integer('row_id')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 5. Leave Requests
        Schema::create('hr_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_row_id')->index();
            $table->unsignedBigInteger('leave_type_id')->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0:Pending, 1:Approved, 2:Rejected');
            $table->text('admin_reply_notes')->nullable();
            $table->boolean('synced_to_sqlserver')->default(false);
            $table->timestamps();

            $table->foreign('leave_type_id')->references('id')->on('hr_leave_types')->onDelete('cascade');
        });

        // 6. Documents
        Schema::create('hr_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_row_id')->index();
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->boolean('synced_to_sqlserver')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_documents');
        Schema::dropIfExists('hr_leave_requests');
        Schema::dropIfExists('hr_leave_types');
        Schema::dropIfExists('hr_attendance_records');
        Schema::dropIfExists('hr_employees');
        Schema::dropIfExists('hr_employee_categories');
    }
};
