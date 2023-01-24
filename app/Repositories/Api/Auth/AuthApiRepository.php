<?php

namespace App\Repositories\Api\Auth;


use App\Entities\HttpCode;
use App\Entities\IsActive;
use App\Entities\Status;
use App\Entities\UserRoles;
use App\Http\Resources\UserAuthResource;
use App\Models\Contact;
use App\Models\Devices;
use App\Models\User;
use App\Models\VerificationCode;
use App\Repositories\General\UtilsRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthApiRepository
{

    // process user login
    public static function signup(array $data)
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRoles::FAN,
            'phone' => isset($data['phone']) ? $data['phone'] : null,
            'status' => Status::UNVERIFIED,
            'lang' => App::getLocale(),
        ];

        $user = User::create($userData);
        if ($user) {
            if (isset($data['device_token'])) {
                $deviceData = [
                    'user_id' => $user->id,
                    'device_type' => isset($data['device_type']) ? $data['device_type'] : null,
                    'device_token' => isset($data['device_token']) ? $data['device_token'] : null,
                ];
                Devices::create($deviceData);
            }
            // 1- send verification email
            self::sendVerificationCode($user);

            // return success response
            return [
                'message' => trans('api.create_account_success_message'),
                'code' => HttpCode::SUCCESS
            ];
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    // get user data
    public static function getUserData($id, $token = false)
    {
        $user = User::where(['id' => $id])
            ->first(['id', 'name', 'email', 'phone', 'phonecode', 'status', 'lang', 'city_id']);
        if ($token) {
            $user->token = $user->createToken('damain')->accessToken;
        }
        $user->city_name = @$user->city->name;
        $country = @$user->city->counyry;
        $user->country_id = $country ? $country->id : null;
        $user->country_name = $country ? $country->name : null;
        $user->image = ($user->image !== null) ? url($user->image) : null;
        return $user;
    }

    // process user login
    public static function login(array $data)
    {
        $remember = (isset($data['remember']) && $data['remember']);
        $user = null;
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            $user = auth()->user();
            if (isset($data['device_token'])) {
                $device = Devices::where([
                    'user_id' => $user->id,
                    'device_token' => isset($data['device_token']) ? $data['device_token'] : null,
                ])->first();
                $deviceData = [
                    'user_id' => $user->id,
                    'device_type' => isset($data['device_type']) ? $data['device_type'] : null,
                    'device_token' => isset($data['device_token']) ? $data['device_token'] : null,
                ];
                if ($device) {
                    $device->update($deviceData);
                } else {
                    Devices::create($deviceData);
                }
            }
        } else {
            return Response()->json([
                'message' => trans('api.credentials_error_message')
            ], HttpCode::ERROR);
        }


        if ($user && in_array( $user->role , [UserRoles::FAN, UserRoles::COACH, UserRoles::CoachGK, UserRoles::CoachGKJunior, UserRoles::OFFICIAL])) {
            if ($user && $user->isBlocked()) {
                return Response()->json([
                    'message' => trans('api.block_status_error_message')
                ], HttpCode::ERROR);
            } else if ($user && $user->isActiveUser()) {
                $user = UserAuthResource::make($user);
                return Response()->json([
                    'data' => $user,
                    'message' => trans('api.login_success_message'),
                ], HttpCode::SUCCESS);
            } else if ($user && $user->isNotPhoneVerified()) {
                // send verification sms
                self::sendVerificationCode($user);
                return Response()->json([
                    'data' => [
                        'verify' => 1
                    ],
                    'message' => trans('api.not_verified_error_message'),
                ], HttpCode::NOT_VERIFIED);
            }
        }
        return Response()->json([
            'message' => trans('api.credentials_error_message')
        ], HttpCode::ERROR);
    }

    // forget password
    public static function forgetPassword(array $data)
    {
        $user = User::where(['email' => $data ['email']])->first();
        if ($user) {
            $is_sent = self::sendVerificationCode($user);
            if ($is_sent) {
                return [
                    'message' => trans('api.forget_password_success_message'),
                    'code' => HttpCode::SUCCESS
                ];
            } else {
                return [
                    'message' => trans('api.general_error_message'),
                    'code' => HttpCode::ERROR
                ];
            }
        }
        return [
            'message' => trans('api.not_found_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    // change password for forget password process
    public static function changeForgetPassword(array $data)
    {
        $user = User::where(['email' => $data ['email']])->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
            $user = UserAuthResource::make($user);
            return [
                'data' => $user,
                'message' => trans('api.change_password_success_message'),
                'code' => HttpCode::SUCCESS
            ];
        }
        return [
            'message' => trans('api.not_found_error_message'),
            'code' => HttpCode::ERROR
        ];
    }


    // logout current user
    public static function logout($data)
    {
        $user = Auth::user();
        if ($user) {
            Devices::where([
                'user_id' => $user->id,
                'device_type' => $data['device_type'],
                'device_token' => $data['device_token']
            ])->forceDelete();
            if ($user->token()) {
                $user->token()->revoke();
                $user->token()->delete();
            }
        }
        return [
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    // get verification code
    public static function getVerificationCode(array $data)
    {
        $user = User::where(['email' => $data['email']])
            ->orWhere(function ($query) use ($data) {
                $query->where(['edited_email' => $data['email']]);
            })
            ->first();
        if ($user) {
            $verificationCode = VerificationCode::where(['user_id' => $user->id])->first();
            if ($verificationCode) {
                return [
                    'data' => [
                        'code' => $verificationCode->code
                    ],
                    'message' => 'success',
                    'code' => HttpCode::SUCCESS
                ];
            }
        }
        return [
            'message' => 'error',
            'code' => HttpCode::ERROR
        ];
    }

    // resend verification code
    public static function resendVerificationCode(array $data)
    {

        $user = User::where(['email' => $data['email']])
            ->orWhere(function ($query) use ($data) {
                $query->where(['edited_email' => $data['email']]);
            })
            ->first();
        if ($user) {
            $is_sent = self::sendVerificationCode($user);
            if ($is_sent) {
                return [
                    'message' => trans('api.resend_success_message'),
                    'code' => HttpCode::SUCCESS
                ];
            }
        }

        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    // check verification code
    public static function checkVerificationCode(array $data)
    {
        $user = User::where(['email' => $data['email']])
            ->orWhere(function ($query) use ($data) {
                $query->where(['edited_email' => $data['email']]);
            })
            ->first();
        if ($user) {
            $verificationCode = VerificationCode::where(['user_id' => $user->id,
                'code' => $data['code']])->first();
            if ($verificationCode) {
                $verificationCode->forceDelete();
                $verify = false;
                $response = [
                    'code' => HttpCode::SUCCESS
                ];
                if ($user->edited_email !== null) {
                    $user->update([
                        'email' => $user->edited_email,
                        'edited_email' => null,
                    ]);
                    $verify = true;
                } else if ($user->status === Status::UNVERIFIED) {
                    $user->update([
                        'status' => Status::ACTIVE
                    ]);
                    $verify = true;
                }
                if ($verify) {
                    $user = UserAuthResource::make($user);
                    $response['data'] = $user;
                    $response['message'] = trans('api.verify_success_message');
                } else {
                    $response['message'] = trans('api.verify_code_success_message');
                }
                return $response;
            }
            return [
                'message' => trans('api.verify_error_message'),
                'code' => HttpCode::ERROR
            ];
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    // send verification code sms to user
    public static function sendVerificationCode($user)
    {
        VerificationCode::where(['user_id' => $user->id])->forceDelete();
        $code = self::createUserVerificationCode($user);
        $verificationCode = VerificationCode::create([
            'user_id' => $user->id,
            'code' => $code
        ]);
        if ($verificationCode) {
            // send email
            $email = ($user->edited_email !== null) ? $user->edited_email : $user->email;
            $locale = App::getLocale();

            UtilsRepository::sendEmail([
                'code' => $code,
                'user' => $user,
                'email' => $email,
                'template' => 'general.email.verify',
                'subject' => trans('api.account_verification_subject')
            ]);
//            $message = str_replace('{code}', $code, trans('sms.your_code'));
//            SendSMSJob::dispatch($phone, $message, $locale);
            return true;
        } else {
            return false;
        }
    }

    // create unique verification code
    public static function createUserVerificationCode($user)
    {
        $code = UtilsRepository::createVerificationCode($user->id, 4);
        if (VerificationCode::where(['code' => $code])->first()) {
            $code = self::createUserVerificationCode($user);
        }
        return $code;
    }

    public static function deleteAccount($data)
    {
        $user = Auth::user();
        if (!Hash::check($data['password'], $user->password)) {
            return [
                'message' => trans('api.old_password_message'),
                'code' => HttpCode::ERROR
            ];
        }
        $user->contacts()->forceDelete();
        $user->forceDelete();
        return [
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

}
