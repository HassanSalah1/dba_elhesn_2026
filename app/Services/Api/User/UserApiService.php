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

}
