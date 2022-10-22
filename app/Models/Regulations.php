<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Regulations extends Model
{
    use HasFactory;
    protected $table = 'regulations';
    protected $fillable = ['name_ar' , 'name_en' , 'description_ar' , 'description_en' , 'file' , 'order'];

    protected $appends = ['name', 'description'];

    public function getNameAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->name_en : $this->name_ar;
    }

    public function getDescriptionAttribute()
    {
        $lang = App::getLocale();
        return $lang === 'en' ? $this->description_en : $this->description_ar;
    }

}
