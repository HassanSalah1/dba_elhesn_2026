<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $table = 'clubs';

    protected $fillable = [
        'row_id',
        'name_ar',
        'name_en',
        'logo',
    ];
}
