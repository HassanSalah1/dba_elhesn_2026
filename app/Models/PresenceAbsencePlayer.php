<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenceAbsencePlayer extends Model
{
    use HasFactory;

    protected $table = 'presence_absence_players';
    protected $fillable = [
        'presence_absence_id',
        'player_id',
        'attendance_status',
        'notes'
    ];
}
