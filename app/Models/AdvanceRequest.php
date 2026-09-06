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

    public function getSeasonAttribute(): ?string
    {
        $teamName = $this->team_name ?: ($this->user_team ? ($this->user_team->full_team_name ?: ($this->user_team->team ? $this->user_team->team->name : null)) : ($this->sport_team ? $this->sport_team->name_ar : null));

        if (!empty($teamName) && preg_match('/^(\d{4}[-\/]\d{4})/', $teamName, $matches)) {
            return $matches[1];
        }

        if (!empty($this->move_date) && $this->move_date !== '1970-01-01') {
            $season = Season::where('start_date', '<=', $this->move_date)
                ->where('end_date', '>=', $this->move_date)
                ->value('name');
            if ($season) {
                return $season;
            }
        }

        return null;
    }
}
