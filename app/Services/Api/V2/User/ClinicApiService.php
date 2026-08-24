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

    public static function createBooking(array $data, $user = null)
    {
        $keys = [
            'booking_date' => 'required|date|date_format:Y-m-d',
            'time_slot_id' => 'required|integer',
            'is_for_other' => 'required|in:0,1',
            'other_name' => 'required_if:is_for_other,1|nullable|string',
            'other_phone' => 'required_if:is_for_other,1|nullable|string',
            'other_country_code' => 'required_if:is_for_other,1|nullable|string',
            'description' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,png,pdf|max:5120',
        ];

        // If user is not logged in, require patient_name and patient_phone
        if (!$user) {
            $keys['patient_name'] = 'required|string';
            $keys['patient_phone'] = 'required|string';
        } else {
            $keys['patient_name'] = 'nullable|string';
            $keys['patient_phone'] = 'nullable|string';
        }

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

        $response = ClinicApiRepository::createBooking($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getBookings(array $data, $user = null)
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

        $response = ClinicApiRepository::getBookings($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getAdminBookings(array $data, $user)
    {
        $keys = [
            'status' => 'nullable|string',
            'date'   => 'nullable|date|date_format:Y-m-d',
            'search' => 'nullable|string',
        ];
        $messages = [
            'string' => trans('api.general_error_message'),
            'date'   => trans('api.general_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::getAdminBookings($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getBookingDetails(array $data, $user = null)
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

        $response = ClinicApiRepository::getBookingDetails($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function cancelBooking(array $data, $user = null)
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

        $response = ClinicApiRepository::cancelBooking($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function updateBookingStatus(array $data, $user)
    {
        $keys = [
            'id'     => 'required|integer',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
            'in'       => trans('api.general_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = ClinicApiRepository::updateBookingStatus($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function addAttachment(array $data, $user = null)
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

        $response = ClinicApiRepository::addAttachment($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function deleteAttachment(array $data, $user = null)
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

        $response = ClinicApiRepository::deleteAttachment($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }
}
