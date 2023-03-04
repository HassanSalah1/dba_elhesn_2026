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

    public function getTeamPlayers(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return UserApiService::getTeamPlayers($data);
    }


    public function getReasons(Request $request)
    {
        $data = $request->all();
        return UserApiService::getReasons($data);
    }

    public function presenceAbsence(Request $request)
    {
        $data = $request->all();
        return UserApiService::presenceAbsence($data);
    }

    public function generalEvaluation(Request $request)
    {
        $data = $request->all();
        return UserApiService::generalEvaluation($data);
    }

    public function getSports(Request $request)
    {
        $data = $request->all();
        return UserApiService::getSports($data);
    }

    public function coachEvaluation(Request $request)
    {
        $data = $request->all();
        return UserApiService::coachEvaluation($data);
    }
    public function getSeasons(Request $request)
    {
        $data = $request->all();
        return UserApiService::getSeasons($data);
    }


}

?>
