<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrDocument extends Model
{
    const STATUS_WAITING_FOR_APPROVAL = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REJECTED = 3;

    protected $table = 'hr_documents';

    protected $fillable = [
        'employee_row_id',
        'description',
        'attachment_path',
        'status_id',
        'synced_to_sqlserver',
    ];

    protected $casts = [
        'status_id' => 'integer',
        'synced_to_sqlserver' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_row_id', 'row_id');
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status_id) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Waiting for Approval',
        };
    }

    public function getStatusTextArAttribute(): string
    {
        return match ($this->status_id) {
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            default => 'قيد الانتظار',
        };
    }
}
