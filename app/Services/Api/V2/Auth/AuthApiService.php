<?php
 
namespace App\Services\Api\V2\Auth;
 
use App\Repositories\Api\V2\Auth\AuthApiRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;
 
class AuthApiService
{
 
    public static function signup(array $data)
    {
        $keys = [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ];
        if (isset($data['phone'])) {
            $keys['phone'] = 'phone:AE,mobile|unique:users';
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
        $response = AuthApiRepository::signup($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function login(array $data)
    {
        $keys = [
            'email' => 'required',
            'password' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        return AuthApiRepository::login($data);
    }
 
    public static function getVerificationCode(array $data)
    {
        $keys = [
            'email' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = AuthApiRepository::getVerificationCode($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function resendVerificationCode(array $data)
    {
        $keys = [
            'email' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = AuthApiRepository::resendVerificationCode($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function checkVerificationCode(array $data)
    {
        $keys = [
            'email' => 'required',
            'code' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = AuthApiRepository::checkVerificationCode($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function forgetPassword(array $data)
    {
        $keys = [
            'email' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = AuthApiRepository::forgetPassword($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function changeForgetPassword(array $data)
    {
        $keys = [
            'email' => 'required',
            'password' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = AuthApiRepository::changeForgetPassword($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
 
    public static function logout($data)
    {
        $keys = [
            'device_type' => 'required',
            'device_token' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = AuthApiRepository::logout($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
    public static function deleteAccount($data)
    {
        $keys = [
            'password' => 'required',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = AuthApiRepository::deleteAccount($data);
        return UtilsRepository::handleResponseApi($response);
    }
 
}
