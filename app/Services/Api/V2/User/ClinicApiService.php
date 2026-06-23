<?php

namespace App\Services\Api\V2\User;

use App\Repositories\Api\V2\User\ClinicApiRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class ClinicApiService
{
    public static function getTimeSlots(array $data)
    {
        $keys = [
            'date' => 'nullable|date|date_format:Y-m-d'
        ];
        $messages = [
            'date' => trans('api.general_error_message'),
            'date_format' => trans('api.general_error_message'),
        ];
        
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::getTimeSlots($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function createBooking(array $data)
    {
        $keys = [
            'booking_date' => 'required|date|date_format:Y-m-d',
            'time_slot_id' => 'required|integer',
            'is_for_other' => 'required|in:0,1',
            'other_name' => 'required_if:is_for_other,1|nullable|string',
            'other_phone' => 'required_if:is_for_other,1|nullable|string',
            'other_country_code' => 'required_if:is_for_other,1|nullable|string',
            'injury_type' => 'required|string',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,png,pdf|max:5120',
        ];

        $messages = [
            'required' => trans('api.required_error_message'),
            'required_if' => trans('api.required_error_message'),
            'mimes' => trans('api.invalid_file_type'),
            'max' => trans('api.file_too_large'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::createBooking($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getBookings(array $data)
    {
        $keys = [
            'status' => 'nullable|string',
        ];
        $messages = [
            'string' => trans('api.general_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::getBookings($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getBookingDetails(array $data)
    {
        $keys = [
            'id' => 'required|integer',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::getBookingDetails($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function cancelBooking(array $data)
    {
        $keys = [
            'id' => 'required|integer',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::cancelBooking($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function addAttachment(array $data)
    {
        $keys = [
            'id' => 'required|integer',
            'file' => 'required|file|mimes:jpeg,png,pdf|max:5120',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
            'mimes' => trans('api.invalid_file_type'),
            'max' => trans('api.file_too_large'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::addAttachment($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function deleteAttachment(array $data)
    {
        $keys = [
            'id' => 'required|integer',
            'attachment_id' => 'required|integer',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::deleteAttachment($data);
        return UtilsRepository::handleResponseApi($response);
    }
}
