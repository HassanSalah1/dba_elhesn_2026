<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';
    protected $fillable = [
        'title_ar', 'title_en',
        'name_ar', 'name_en',
        'position_ar', 'position_en',
        'image', 'order'
    ];

    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->title_en ?: $this->title_ar) : $this->title_ar;
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->name_en ?: $this->name_ar) : $this->name_ar;
    }

    public function getPositionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->position_en ?: $this->position_ar) : $this->position_ar;
    }

    public function getImageUrlAttribute()
    {
        $image = $this->image ? $this->image : 'images/default.png';
        return url($image);
    }
}
