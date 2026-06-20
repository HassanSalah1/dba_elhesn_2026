<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class TeamPlayer extends Model
{
    use HasFactory;

    protected $table = 'team_players';
    protected $fillable = ['team_id', 'player_id', 'number', 'name_en', 'name_ar', 'image',
        'birth_date', 'nationality_ar', 'nationality_en', 'height', 'weight',
        'position_ar', 'position_en', 'goals', 'wins', 'losses',
        'matches_played', 'minutes_played', 'yellow_cards', 'red_cards'];
    protected $appends = ['name', 'position', 'nationality'];
    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getImageUrlAttribute()
    {
        $image = $this->image ? $this->image : 'images/default.png';
        return url($image);
    }

    public function getNameAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->name_en : $this->name_ar;
    }

    public function getNationalityAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->nationality_en : $this->nationality_ar;
    }

    public function getPositionAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->position_en : $this->position_ar;
    }

    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }
        return $this->birth_date->age;
    }

    public function team(){
        return $this->belongsTo(SportTeam::class , 'team_id');
    }
}
