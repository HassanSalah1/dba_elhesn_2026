<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendReason extends Model
{
    use HasFactory;

    protected $table = 'attend_reasons';

    protected $fillable = [
        'row_id',
        'reason',
        'reason_key',
        'the_order',
        'global_reason',
    ];
}
