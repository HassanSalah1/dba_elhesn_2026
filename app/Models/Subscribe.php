<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscribe extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "full_name_ar",
        "full_name_en",
        "birth_date",
        "nationality",
        "birth_place",
        "category",
        "identification_number",
        "id_expiration_date",
        "passport_number",
        "passport_expiry",
        "address",
        "guardian_phone",
        "another_club",
        "sport_id",
        "level",
        "weight",
        "height",
        "clothes_size",
        "shoe_size",
        "vaccine_number",
        "school",
        "guardian_job",
        "first_dose_date",
        "second_dose_date",
        "third_dose_date",
        "personal_image",
        "id_photo",
        "player_passport_photo",
        "parent_id_photo",
        "parent_passport_photo",
        "player_parent_residence_photo",
        "medical_examination_photo",
        "parent_acknowledgment_photo",
        "player_birth_certificate_photo",
        "player_mother_copy_photo",
        "sponsor_residence_photo"
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
