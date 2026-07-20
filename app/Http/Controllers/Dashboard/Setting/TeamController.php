<?php

namespace App\Http\Controllers\Dashboard\Setting;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Setting\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    //
    public function showTeams()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.teams_title');
        $data['debatable_names'] = array(
            trans('admin.title_ar'), trans('admin.title_en'),
            trans('admin.name_ar'), trans('admin.name_en'),
            trans('admin.position_ar'), trans('admin.position_en'),
            trans('admin.actions')
        );
        return view('admin.settings.teams')->with($data);
    }

    public function getTeamsData(Request $request)
    {
        $data = $request->all();
        return TeamService::getTeamsData($data);
    }

    public function addTeam(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return TeamService::addTeam($data);
    }

    public function deleteTeam(Request $request)
    {
        $data = $request->all();
        return TeamService::deleteTeam($data);
    }

    public function restoreTeam(Request $request)
    {
        $data = $request->all();
        return TeamService::restoreTeam($data);
    }

    public function getTeamData(Request $request)
    {
        $data = $request->all();
        return TeamService::getTeamData($data);
    }

    public function editTeam(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return TeamService::editTeam($data);
    }

}
