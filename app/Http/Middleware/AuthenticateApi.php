<?php

namespace App\Http\Middleware;


use App\Entities\HttpCode;
use App\Entities\Status;
use App\Entities\UserRoles;
use App\Repositories\General\UtilsRepository;
use Closure;

class AuthenticateApi
{
    public function handle($request, Closure $next, $role = null)
    {
        $user = auth()->user();

        if (!$user || $user->status === Status::INACTIVE || $user->role === UserRoles::ADMIN) {
            return UtilsRepository::handleResponseApi([
                'message' => trans('api.not_login_message'),
                'code' => HttpCode::AUTH_ERROR
            ]);
        } else if ($user && $user->status === Status::UNVERIFIED) {
            return UtilsRepository::handleResponseApi([
                'message' => trans('api.verify_account_error_message'),
                'code' => HttpCode::NOT_VERIFIED
            ]);
        }
        return $next($request);
    }
}
