<?php

namespace App\Services\Api\V2\User;

use App\Repositories\Api\V2\User\HrApiRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;
use App\Models\User;

class HrApiService
{
    public static function login(array $data)
    {
        $keys = [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = HrApiRepository::login($data);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getAttendance(array $data, User $user)
    {
        $keys = [
            'month' => 'nullable|integer|between:1,12',
            'year'  => 'nullable|integer|min:2020',
        ];
        $messages = [
            'integer' => trans('api.general_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = HrApiRepository::getAttendance($data, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getLeaveTypes()
    {
        $response = HrApiRepository::getLeaveTypes();
        return UtilsRepository::handleResponseApi($response);
    }

    public static function createLeaveRequest(array $data, User $user, $attachmentFile = null)
    {
        $keys = [
            'leave_type_id' => 'required|integer',
            'start_date'    => 'required|date|date_format:Y-m-d',
            'end_date'      => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'description'   => 'nullable|string',
        ];
        $messages = [
            'required'               => trans('api.required_error_message'),
            'after_or_equal'         => trans('api.general_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = HrApiRepository::createLeaveRequest($data, $user, $attachmentFile);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getLeaveRequests(User $user)
    {
        $response = HrApiRepository::getLeaveRequests($user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getLeaveRequestDetails($id, User $user)
    {
        $response = HrApiRepository::getLeaveRequestDetails($id, $user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function createDocument(array $data, User $user, $attachmentFile = null)
    {
        $keys = [
            'description' => 'required|string',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = HrApiRepository::createDocument($data, $user, $attachmentFile);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getDocuments(User $user)
    {
        $response = HrApiRepository::getDocuments($user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getDocumentDetails($id, User $user)
    {
        $response = HrApiRepository::getDocumentDetails($id, $user);
        return UtilsRepository::handleResponseApi($response);
    }
}
