<?php

namespace App\Models;

use App\Entities\ImageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Action extends Model
{
    use HasFactory;

    protected $table = 'actions';
    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'start_date',
        'end_date',
        'video_url',
        'location_ar',
        'location_en'
    ];

    protected $appends = ['title', 'description', 'location'];

    public function getTitleAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->title_en : $this->title_ar;
    }

    public function getDescriptionAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->description_en : $this->description_ar;
    }

    public function getLocationAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->location_en : $this->location_ar;
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'item_id', 'id')
            ->where(['item_type' => ImageType::ACTION])->get();
    }

    public function image()
    {
        return $this->hasOne(Image::class, 'item_id', 'id')
            ->where(['item_type' => ImageType::ACTION])
            ->where(['primary' => 1])->first();
    }
}
