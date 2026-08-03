<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrDocument extends Model
{
    protected $table = 'hr_documents';

    protected $fillable = [
        'employee_row_id',
        'description',
        'attachment_path',
        'synced_to_sqlserver',
    ];

    protected $casts = [
        'synced_to_sqlserver' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_row_id', 'row_id');
    }
}
