<?php
namespace App\Services\Dashboard\Setting;

use App\Repositories\Dashboard\Setting\SportGameRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class SportGameService
{


    public static function getSportGamesData(array $data)
    {
        return SportGameRepository::getSportGamesData($data);
    }

    public static function addSportGame(array $data)
    {
        $rules = [
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'history_ar' => 'nullable',
            'history_en' => 'nullable',
            'championships_ar' => 'nullable',
            'championships_en' => 'nullable',
            'image' => 'required',
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = SportGameRepository::addSportGame($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function deleteSportGame(array $data)
    {
        $response = SportGameRepository::deleteSportGame($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function restoreSportGame(array $data)
    {
        $response = SportGameRepository::restoreSportGame($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function getSportGameData(array $data)
    {
        $response = SportGameRepository::getSportGameData($data);
        return UtilsRepository::response($response);
    }

    public static function editSportGame(array $data)
    {
        $rules = [
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'history_ar' => 'nullable',
            'history_en' => 'nullable',
            'championships_ar' => 'nullable',
            'championships_en' => 'nullable',
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = SportGameRepository::editSportGame($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }
}

?>
