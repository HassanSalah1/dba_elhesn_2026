<?php
namespace App\Services\Dashboard\Setting;

use App\Repositories\Dashboard\Setting\TeamRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class TeamService
{


    public static function getTeamsData(array $data)
    {
        return TeamRepository::getTeamsData($data);
    }

    public static function addTeam(array $data)
    {
        $rules = [
            'title_ar' => 'required',
            'name_ar' => 'required',
            'position_ar' => 'required',
            'title_en' => 'nullable',
            'name_en' => 'nullable',
            'position_en' => 'nullable',
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = TeamRepository::addTeam($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function deleteTeam(array $data)
    {
        $response = TeamRepository::deleteTeam($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function restoreTeam(array $data)
    {
        $response = TeamRepository::restoreTeam($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function getTeamData(array $data)
    {
        $response = TeamRepository::getTeamData($data);
        return UtilsRepository::response($response);
    }

    public static function editTeam(array $data)
    {
        $rules = [
            'title_ar' => 'required',
            'name_ar' => 'required',
            'position_ar' => 'required',
            'title_en' => 'nullable',
            'name_en' => 'nullable',
            'position_en' => 'nullable',
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = TeamRepository::editTeam($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }
}

?>
