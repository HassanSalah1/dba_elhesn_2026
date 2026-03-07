<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Auth\AuthService;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{


    public function showCard()
    {
        return view('pdf.profile_card');
    }

    // Login Cover
    public function showLogin()
    {
        $user = auth()->user();
        if ($user && ($user->isDashboardAuth() || $user->isEmployeeAuth())) {
            return redirect()->to(url('/admin/home'));
        }
        $pageConfigs = [
            'blankPage' => true,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $title = trans('admin.login_title');
        return view('admin.auth.login', [
            'pageConfigs' => $pageConfigs,
            'title' => $title
        ]);
    }

    public function processLogin(Request $request)
    {
        $data = $request->all('email', 'password');
        $data['remember'] = $request->has('remember');
        $response = AuthService::processLogin($data);

        if ($request->expectsJson() || $request->ajax()) {
            return $response;
        }

        $content = $response->getData(true);
        $success = $response->getStatusCode() === 200;
        if ($success) {
            return redirect()->to(url('/admin/home?first_time=1'));
        }
        return redirect()->back()->withInput()->with('error', $content['message'] ?? trans('admin.general_error_message'));
    }


    public function logout(Request $request)
    {
        $url = url('/admin/auth/login');
        return AuthService::logout($url);
    }

}

?>
