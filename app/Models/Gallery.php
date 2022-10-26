<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';
    protected $fillable = ['video_url', 'image' , 'sport_game_id'];

    public function getUrlAttribute()
    {
        $url = $this->video_url ? $this->video_url : url($this->image);
        return $url;
    }

    public function getTypeAttribute()
    {
        return $this->video_url ? 'video' : 'image';
    }
}
