<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class TeamPlayer extends Model
{
    use HasFactory;

    protected $table = 'team_players';
    protected $fillable = ['team_id', 'player_id', 'name_en', 'name_ar', 'image'];
    protected $appends = ['name'];

    public function getImageUrlAttribute()
    {
        $image = $this->image ? url($this->image) : null; //'images/default.png';
        return $image;
    }

    public function getNameAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->name_en : $this->name_ar;
    }
}
