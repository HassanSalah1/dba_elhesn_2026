<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "player_full_name_ar", "player_full_name_en", "player_photo", "birth_date",
        "birth_place", "nationality", "parent_full_name_ar", "parent_full_name_en",
        "parent_email", "parent_category", "parent_passport_photo",
        "parent_residence_photo", "parent_id_photo", 'guardian_phone',
        "clothes_size", "shoe_size", "is_another_club", "another_club_name", "weight",
        "height", 'sport_id',
        "player_id_number", "player_id_expire", "player_passport_number", "player_passport_expire",
        "player_birth_certificate_photo", "player_phone", "player_school_name", "player_class_name",
        "sport_level", "vaccine_count", "vaccine_1", "vaccine_2", "vaccine_3", "player_passport_photo",
        "player_medical_examination_photo", "player_mother_passport_photo", "player_kafel_passport_photo"
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
