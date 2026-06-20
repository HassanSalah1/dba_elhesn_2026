<?php

namespace App\Services\Dashboard\Setting;

use App\Repositories\Dashboard\Setting\EmergencyRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class EmergencyService
{
    public static function getEmergenciesData(array $data)
    {
        return EmergencyRepository::getEmergenciesData($data);
    }

    public static function addEmergency(array $data)
    {
        $rules = [
            'name_ar' => 'required',
            'name_en' => 'required',
            'phone' => 'required',
            'country_code' => 'required',
            'order' => 'required|numeric'
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = EmergencyRepository::addEmergency($data);
        return UtilsRepository::response($response, trans('admin.process_success_message'), '');
    }

    public static function deleteEmergency(array $data)
    {
        $response = EmergencyRepository::deleteEmergency($data);
        return UtilsRepository::response($response, trans('admin.process_success_message'), '');
    }

    public static function getEmergencyData(array $data)
    {
        $response = EmergencyRepository::getEmergencyData($data);
        return UtilsRepository::response($response);
    }

    public static function editEmergency(array $data)
    {
        $rules = [
            'name_ar' => 'required',
            'name_en' => 'required',
            'phone' => 'required',
            'country_code' => 'required',
            'order' => 'required|numeric'
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = EmergencyRepository::editEmergency($data);
        return UtilsRepository::response($response, trans('admin.process_success_message'), '');
    }
}
