<?php
namespace App\Repositories\Dashboard\Auth;

use App\Entities\Status;
use App\Entities\UserRoles;
use App\Models\User\User;
use App\Models\VerificationCode;
use App\Repositories\General\UtilsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthRepository
{

    // process login attempt
    // @input array [ 'email' , 'password' , 'remember']
    // return JsonResponse

    public static function processLogin(array $arr)
    {
        if (Auth::attempt(['email' => $arr['email'], 'password' => $arr['password']], true)) {
            $user = auth()->user();
            if ($user && $user->status === Status::ACTIVE && ($user->isDashboardAuth() || $user->isEmployeeAuth())) {
                return true;
            } else {
                return trans('admin.blocked_user_error_message');
            }
        }
        return trans('admin.auth_password_error_message');
    }

    // check if auth or not
    // return bool or abort 404 error page.

    public static function isAuthorized()
    {
        if (Auth::check()) {
            $role = auth()->user()->role;
            if ($role === UserRoles::ADMIN)
                return true;
            else
                abort(404);
        }
        return false;
    }


    // logout current user
    // then redirect user to url
    // @input url
    public static function logout($url)
    {
        $user = auth()->user();
        if ($user) {
            $user->update([
                'device_type' => null,
                'device_token' => null
            ]);
        }
        Auth::logout();
        return redirect()->to($url);
    }

    public static function updateProfile(array $data)
    {
        if (isset($data['old_password']) && !empty($data['old_password'])) {
            $data['password'] = bcrypt($data['password']);
        } else
            unset($data['password']);

        $image_name = 'image';
        if ($data['request']->hasFile($image_name)) {
            $file_id = 'img_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));

            $image_path = 'uploads/users/';
            $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id);
            if ($image !== false) {
                if (file_exists($data['user']->admin_image))
                    unlink($data['user']->admin_image);
                $data['admin_image'] = $image;
            } else {
                unset($data['image']);
            }
        } else {
            unset($data['image']);
        }

        $updated = $data['user']->update($data);
        if (isset($updated) && $updated) {
            $data['user']->admin_image = ($data['user']->admin_image !== null) ?
                url($data['user']->admin_image) : url('/dashboard/images/users/avatar-1.jpg');
            return $data['user'];
        }
        return false;
    }

    public static function processResetPassword(array $data)
    {
        $email = $data['email'];
        $user = User::where(['email' => $email])->first();
        if ($user) {
            $id = $user['id'];
            $code = UtilsRepository::createVerificationCode($id, 15);
            UtilsRepository::sendEmail([
                'code' => $code,
                'user' => $user,
                'email' => $user->email,
                'template' => 'general.email.forget_password',
                'subject' => trans('admin.forget_password')
            ]);
            $user->verification_code()->delete();
            $user->verification_code()->create([
                'user_id' => $id,
                'code' => $code
            ]);
            return response()->json([
                'message' => trans('admin.email_sent')
            ]);
        } else {
            return response()->json([
                'error' => trans('admin.unavailable_email')
            ], 403);
        }
    }

    public static function processChangePassword(array $data)
    {
        $verification = VerificationCode::where(['code' => $data['code']])->first();
        if ($verification) {
            $user = User::where(['id' => $verification->user_id])->first();
            $user->update([
                'password' => Hash::make($data['password'])
            ]);
            $verification->forceDelete();
            return true;
        }
        return false;
    }

}

?>
