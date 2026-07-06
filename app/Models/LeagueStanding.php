<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeagueStanding extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_row_id',
        'club_id',
        'team_name',
        'play',
        'win',
        'draw',
        'lose',
        'goals_for',
        'goals_against',
        'goals_diff',
        'points',
        'rank',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id', 'row_id');
    }
}
