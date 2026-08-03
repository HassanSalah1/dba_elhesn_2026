<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrLeaveType extends Model
{
    protected $table = 'hr_leave_types';

    protected $fillable = [
        'row_id',
        'name_ar',
        'name_en',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
