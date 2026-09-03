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
        'team_name',
        'user_id',
        'players_count',
        'escorts_count',
        'cost',
        'location',
        'statement',
        'details',
        'tournament',
        'match_timing',
        'leave_time',
        'move_date',
        'return_date',
        'breakfast',
        'lunch',
        'dinner',
        'snacks',
        'breakfast_count',
        'breakfast_cost',
        'lunch_count',
        'lunch_cost',
        'dinner_count',
        'dinner_cost',
        'snack_count',
        'snack_cost',
        'type',
        'status',
        'synced_to_sqlserver',
    ];

    protected $casts = [
        'synced_to_sqlserver' => 'boolean',
        'cost' => 'decimal:2',
        'breakfast_cost' => 'decimal:2',
        'lunch_cost' => 'decimal:2',
        'dinner_cost' => 'decimal:2',
        'snack_cost' => 'decimal:2',
        'breakfast_count' => 'integer',
        'lunch_count' => 'integer',
        'dinner_count' => 'integer',
        'snack_count' => 'integer',
    ];

    public function user_team()
    {
        return $this->belongsTo(UserTeam::class, 'user_team_id');
    }

    public function sport_team()
    {
        return $this->belongsTo(SportTeam::class, 'team_row_id', 'team_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
