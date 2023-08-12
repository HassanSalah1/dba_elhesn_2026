<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Services\Api\Setting\SettingApiService;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function getSiteNews(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getSiteNews($data);
    }

    public function getIntros(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getIntros($data);
    }

    public function getAbout(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getAbout($data);
    }

    public function getMagles(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getMagles($data);
    }

    public function getClubStructure(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getClubStructure($data);
    }

    public function getElders(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getElders($data);
    }

    public function getTerms(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getTerms($data);
    }


    public function getHistory(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getHistory($data);
    }

    public function getContactDetails(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getContactDetails($data);
    }

    public function addContact(Request $request)
    {
        $data = $request->all();
        return SettingApiService::addContact($data);
    }

    public function getCategories(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getCategories($data);
    }

    public function getTeams(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getTeams($data);
    }

    public function getGallery(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getGallery($data);
    }

    public function getSportGames(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getSportGames($data);
    }

    public function getSportGamesGallery(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return SettingApiService::getSportGamesGallery($data);
    }

    public function getCommittees(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getCommittees($data);
    }

    public function getHome(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getHome($data);
    }

    public function getNews(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getNews($data);
    }

    public function getNewDetails(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return SettingApiService::getNewDetails($data);
    }

    public function getActions(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getActions($data);
    }

    public function getActionDetails(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return SettingApiService::getActionDetails($data);
    }

    public function getRegulations(Request $request)
    {
        $data = $request->all();
        return SettingApiService::getRegulations($data);
    }

    public function getSportTeams(Request $request , $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return SettingApiService::getSportTeams($data);
    }

    public function getTeamPlayers(Request $request , $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return SettingApiService::getTeamPlayers($data);
    }

}

?>
