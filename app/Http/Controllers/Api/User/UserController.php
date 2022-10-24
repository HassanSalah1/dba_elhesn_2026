<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\Api\User\UserApiService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function getProfile(Request $request)
    {
        $data = $request->all();
        return UserApiService::getProfile($data);
    }

    public function editProfile(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        $data['user'] = auth()->user();
        return UserApiService::editProfile($data);
    }


    public function downloadProfileCard(Request $request)
    {
        $data = $request->all();
        return UserApiService::downloadProfileCard($data);
    }

    public function getMyNotifications(Request $request)
    {
        $data = $request->all();
        return UserApiService::getMyNotifications($data);
    }

}

?>
