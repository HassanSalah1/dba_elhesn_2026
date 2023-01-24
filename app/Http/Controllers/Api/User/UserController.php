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

    public function getNotificationsCount(Request $request)
    {
        $data = $request->all();
        return UserApiService::getNotificationsCount($data);
    }

    public function subscribeSport(Request $request)
    {
        $data = $request->all();
        return UserApiService::subscribeSport($data);
    }

    public function uploadImage(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return UserApiService::uploadImage($data);
    }

    public function getMyTeams(Request $request)
    {
        $data = $request->all();
        return UserApiService::getMyTeams($data);
    }

    public function administrativeReport(Request $request)
    {
        $data = $request->all();
        return UserApiService::administrativeReport($data);
    }


    public function advanceRequests(Request $request)
    {
        $data = $request->all();
        return UserApiService::advanceRequests($data);
    }


    public function presenceAbsence(Request $request)
    {
        $data = $request->all();
        return UserApiService::presenceAbsence($data);
    }

}

?>
