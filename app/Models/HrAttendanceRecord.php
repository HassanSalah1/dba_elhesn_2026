<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrAttendanceRecord extends Model
{
    protected $table = 'hr_attendance_records';

    protected $fillable = [
        'row_id',
        'employee_row_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_row_id', 'row_id');
    }
}
