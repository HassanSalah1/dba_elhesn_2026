<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "player_full_name_ar",
        "player_full_name_en",
        "player_photo",
        "birth_date",
        "birth_place",
        "nationality",
        "parent_full_name_ar",
        "parent_full_name_en",
        "parent_email",
        "parent_category",
        "parent_passport_photo",
        "parent_residence_photo",
        "parent_id_photo",
        'guardian_phone',
        "clothes_size",
        "shoe_size",
        "is_another_club",
        "another_club_name",
        "weight",
        "height",
        'sport_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sport()
    {
        return $this->belongsTo(SportGame::class, 'sport_id');
    }
}
