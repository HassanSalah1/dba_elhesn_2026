<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceRequest extends Model
{
    use HasFactory;

    protected $table = 'advance_requests';
    protected $fillable = [
        'row_id',
        'user_team_id',
        'team_row_id',
        'user_id',
        'players_count',
        'escorts_count',
        'cost',
        'location',
        'statement',
        'details',
        'tournament',
        'match_timing',
        'move_date',
        'return_date',
        'breakfast',
        'lunch',
        'dinner',
        'snacks',
        'type',
        'status',
        'synced_to_sqlserver',
    ];

    protected $casts = [
        'synced_to_sqlserver' => 'boolean',
        'cost' => 'decimal:2',
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
