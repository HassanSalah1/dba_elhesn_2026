<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'row_id',
        'season_row_id',
        'name_ar',
        'name_en',
        'sport_id',
        'weeks_no',
        'logo',
        'mobile_app_header_comp',
    ];

    public function matches()
    {
        return $this->hasMany(SportMatch::class, 'competition_row_id', 'row_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_row_id', 'row_id');
    }
}
