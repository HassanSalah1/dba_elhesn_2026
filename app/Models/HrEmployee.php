<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrEmployee extends Model
{
    protected $table = 'hr_employees';

    protected $fillable = [
        'row_id',
        'user_id',
        'category_id',
        'name_ar',
        'name_en',
        'job_title',
        'photo',
        'username',
        'hr_admin',
        'password_hash',
    ];

    protected $casts = [
        'hr_admin' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(HrEmployeeCategory::class, 'category_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(HrAttendanceRecord::class, 'employee_row_id', 'row_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(HrLeaveRequest::class, 'employee_row_id', 'row_id');
    }

    public function documents()
    {
        return $this->hasMany(HrDocument::class, 'employee_row_id', 'row_id');
    }
}
