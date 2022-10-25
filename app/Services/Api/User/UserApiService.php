<?php

namespace App\Services\Api\User;


use App\Entities\HttpCode;
use App\Repositories\Api\User\UserApiRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;
use Illuminate\Support\Facades\Hash;

class UserApiService
{


    public static function getProfile(array $data)
    {
        $response = UserApiRepository::getProfile($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function editProfile(array $data)
    {
        $keys = [];
        if (isset($data['email']) && $data['user']->email !== $data['email']) {
            $keys['email'] = 'unique:users';
        }
        if (isset($data['phone']) && $data['user']->phone !== $data['phone']) {
            $keys['phone'] = 'unique:users|phone:AE,mobile';
        }
        if (isset($data['password']) || isset($data['old_password'])) {
            $keys = [
                'password' => 'required',
                'old_password' => 'required',
            ];
        }
        $messages = [
            'required' => trans('api.required_error_message'),
            'email.unique' => trans('api.email_unique_error_message'),
            'phone.unique' => trans('api.phone_unique_error_message'),
            'phone' => trans('api.valid_phone_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        if (isset($data['password']) && isset($data['old_password'])) {
            if (Hash::check($data['old_password'], $data['user']->password)) {
                $data['password'] = bcrypt($data['password']);
                unset($data['old_password']);
            } else {
                return UtilsRepository::handleResponseApi([
                    'message' => trans('api.old_password_message'),
                    'code' => HttpCode::ERROR
                ]);
            }
        }
        $response = UserApiRepository::editProfile($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function downloadProfileCard(array $data)
    {
        return UserApiRepository::downloadProfileCard($data);
    }

    public static function getMyNotifications(array $data)
    {
        $response = UserApiRepository::getMyNotifications($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function subscribeSport(array $data)
    {
        $keys = [
            'full_name_ar' => 'required',
            'full_name_en' => 'required',
            'birth_date' => 'required',
            'nationality' => 'required',
            'birth_place' => 'required',
            'category' => 'required',
            'identification_number' => 'required',
            'id_expiration_date' => 'required',
            'passport_number' => 'required',
            'passport_expiry' => 'required',
            'address' => 'required',
            'guardian_phone' => 'required',
            'another_club' => 'required',
            'sport_id' => 'required',
            'level' => 'required',
            'weight' => 'required',
            'height' => 'required',
            'clothes_size' => 'required',
            'shoe_size' => 'required',
            'vaccine_number' => 'required',
            'personal_image' => 'required',
            'id_photo' => 'required',
            'player_passport_photo' => 'required',
            'parent_id_photo' => 'required',
            'parent_passport_photo' => 'required',
            'player_parent_residence_photo' => 'required',
            'medical_examination_photo' => 'required',


            // صورة شخصية بملابس رياضية
            // صورة الهوية
            // صورة جواز سفر اللاعب
            // صورة هوية ولى الامر
            // صورة جواز ولى الامر
            // صورة اقامة اللاعب وولى الامر
            // صورة فحص طبى لائق طبيا
            /////
            // اقرار  ولى الامر للموافقة على تسجيل اللاعب - اللاعبين تحت 18 سنه
            // شهادة ميلاد اللاعب
            // صورة جوازة السفر وخلاصة القيد للأم للاعبين أبناء المواطنات
            // صورة جواز السفر والإقامو للكفيل للاعبين المشاركين فى لعبة ألعاب القوى
            //
            //
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = UserApiRepository::subscribeSport($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function uploadImage(array $data)
    {
        $keys = [
            'image' => 'required|mimes:jpg,jpeg,png|max:5120',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
            'mimes' => trans('api.mimes_error_message'),
            'max' => trans('api.max_size_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = UserApiRepository::uploadImage($data);
        return UtilsRepository::handleResponseApi($response);
    }

}
