<?php
namespace App\Services\Dashboard\Setting;

use App\Repositories\Dashboard\Setting\RegulationRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class RegulationService
{


    public static function getRegulationsData(array $data)
    {
        return RegulationRepository::getRegulationsData($data);
    }

    public static function addRegulation(array $data)
    {
        $rules = [
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            "file" => "required|mimes:pdf|max:10000"
        ];
        $messages = [
            'mimes' => trans('admin.file_error_message'),
            'max' => trans('admin.file_error_message'),
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = RegulationRepository::addRegulation($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function deleteRegulation(array $data)
    {
        $response = RegulationRepository::deleteRegulation($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function restoreRegulation(array $data)
    {
        $response = RegulationRepository::restoreRegulation($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function getRegulationData(array $data)
    {
        $response = RegulationRepository::getRegulationData($data);
        return UtilsRepository::response($response);
    }

    public static function editRegulation(array $data)
    {
        $rules = [
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = RegulationRepository::editRegulation($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }
}

?>
