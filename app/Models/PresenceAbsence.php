<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenceAbsence extends Model
{
    use HasFactory;

    protected $table = 'presence_absences';
    protected $fillable = [
        'user_team_id',
        'user_id',
        'date',
        'period'
    ];
}
