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

    public function user_team()
    {
        return $this->belongsTo(UserTeam::class, 'user_team_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
