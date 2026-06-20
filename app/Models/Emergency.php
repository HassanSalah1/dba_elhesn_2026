<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Emergency extends Model
{
    use HasFactory;

    protected $table = 'emergencies';

    protected $fillable = [
        'name_ar',
        'name_en',
        'phone',
        'country_code',
        'order',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        $locale = App::getLocale();
        return $locale === 'en' ? $this->name_en : $this->name_ar;
    }
}
