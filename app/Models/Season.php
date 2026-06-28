<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = [
        'row_id',
        'name',
        'start_date',
        'end_date',
        'notes',
        'active',
    ];
}
