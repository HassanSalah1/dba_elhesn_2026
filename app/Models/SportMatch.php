<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportMatch extends Model
{
    use HasFactory;

    protected $table = 'sport_matches';

    protected $fillable = [
        'row_id',
        'season_row_id',
        'competition_row_id',
        'team1',
        'team1_row_id',
        'team2',
        'team2_row_id',
        'match_date',
        'match_time',
        'stage_round',
        'match_number',
        'week',
        'pitch',
        'remarks',
        'team1_result',
        'team2_result',
        'match_in_house',
        'fanet_match_id',
        'live_link',
    ];

    public function team1Club()
    {
        return $this->belongsTo(Club::class, 'team1_row_id', 'row_id');
    }

    public function team2Club()
    {
        return $this->belongsTo(Club::class, 'team2_row_id', 'row_id');
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competition_row_id', 'row_id');
    }
}
