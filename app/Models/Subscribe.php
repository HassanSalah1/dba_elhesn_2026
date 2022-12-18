<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_email',
        "player_full_name_ar",
        "player_full_name_en",
        "birth_date",
        "nationality",
        "birth_place",
        "player_category",
        "player_id_number",
        "player_id_expire_date",
        "player_passport_number",
        "player_passport_expire_date",
        "address",
        "player_phone",
        "player_school_name",
        "player_class_name",
        "another_club_name",
        'sport_id',
        "sport_level",
        "weight",
        "height",
        "clothes_size",
        "shoe_size",
        "parent_phone",
        "parent_job",
        "player_photo",
        "player_id_photo",
        "player_passport_photo",
        "player_medical_examination_photo",
        "player_birth_certificate_photo",
        "parent_id_photo",
        "parent_passport_photo",
        "parent_residence_photo",
        "parent_acknowledgment_file",
        "player_mother_passport_photo",
        "player_kafel_passport_photo"
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
