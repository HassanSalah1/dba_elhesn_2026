<?php

namespace App\Http\Middleware;

use App\Entities\UserRoles;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthWare
{
    public function handle($request, Closure $next, $role = null)
    {
        $user = \auth()->user();
        if (!$user || (!in_array($user->role, [UserRoles::EMPLOYEE, UserRoles::ADMIN]))) {
            return redirect()->to(url('/admin/auth/login'));
        }
        return $next($request);
    }
}
