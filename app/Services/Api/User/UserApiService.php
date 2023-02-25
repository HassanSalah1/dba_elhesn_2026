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

    public static function getNotificationsCount(array $data)
    {
        $response = UserApiRepository::getNotificationsCount($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function subscribeSport(array $data)
    {
        $keys = [
            'player_email' => 'required',
            "player_full_name_ar" => 'required',
            "player_full_name_en" => 'required',
            "birth_date" => 'required',
            "nationality" => 'required',
            "birth_place" => 'required',
            "player_category" => 'required',
            "player_id_number" => 'required',
            "player_id_expire_date" => 'required',
            "player_passport_number" => 'required',
            "player_passport_expire_date" => 'required',
            "address" => 'required',
            'sport_id' => 'required',
            "sport_level" => 'required',
            "weight" => 'required',
            "height" => 'required',
            "clothes_size" => 'required',
            "shoe_size" => 'required',
            "parent_phone" => 'required',
            "player_photo" => 'required',
            "player_id_photo" => 'required',
            "player_passport_photo" => 'required',
            "player_medical_examination_photo" => 'required',
            "parent_id_photo" => 'required',
            "parent_passport_photo" => 'required'
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
            'image' => 'required|mimes:jpg,jpeg,png,pdf|max:6120',
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

    public static function getMyTeams(array $data)
    {
        $response = UserApiRepository::getMyTeams($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function administrativeReport(array $data)
    {
        $keys = [
            'team_id' => 'required',
            'date' => 'required',
            'subject' => 'required'
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = UserApiRepository::administrativeReport($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function advanceRequests(array $data)
    {
        $keys = [
            'team_id' => 'required',
            'players_count' => 'required',
            'escorts_count' => 'required',
            'cost' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = UserApiRepository::advanceRequests($data);
        return UtilsRepository::handleResponseApi($response);
    }


    public static function getTeamPlayers(array $data)
    {
        $response = UserApiRepository::getTeamPlayers($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getReasons(array $data)
    {
        $response = UserApiRepository::getReasons($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function presenceAbsence(array $data)
    {
        $keys = [
            'team_id' => 'required',
            'date' => 'required',
            'period' => 'required',
            'players' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = UserApiRepository::presenceAbsence($data);
        return UtilsRepository::handleResponseApi($response);
    }

}
