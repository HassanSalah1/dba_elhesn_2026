<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrLeaveRequest extends Model
{
    protected $table = 'hr_leave_requests';

    protected $fillable = [
        'employee_row_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'description',
        'attachment_path',
        'status',
        'admin_reply_notes',
        'synced_to_sqlserver',
    ];

    protected $casts = [
        'synced_to_sqlserver' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_row_id', 'row_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(HrLeaveType::class, 'leave_type_id');
    }
}
