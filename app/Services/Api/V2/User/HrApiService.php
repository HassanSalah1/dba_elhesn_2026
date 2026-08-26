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
            'year' => 'nullable|integer|min:2020',
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
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:10240',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
            'after_or_equal' => trans('api.leave_end_date_after_or_equal') ?: 'تاريخ نهاية الإجازة يجب أن يكون مساوياً لتاريخ بداية الإجازة أو بعده',
            'end_date.after_or_equal' => trans('api.leave_end_date_after_or_equal') ?: 'تاريخ نهاية الإجازة يجب أن يكون مساوياً لتاريخ بداية الإجازة أو بعده',
            'date_format' => trans('api.date_format_error_message') ?: 'صيغة التاريخ غير صحيحة',
            'mimes' => trans('api.invalid_hr_file_type') ?: 'الصيغ المتاحة للملفات هي: PDF, PNG, JPG, JPEG, DOC, DOCX',
            'max' => trans('api.file_too_large_10mb') ?: 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
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

    public static function getAdminLeaveRequests(User $user)
    {
        $response = HrApiRepository::getAdminLeaveRequests($user);
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
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:10240',
        ];
        $messages = [
            'required' => trans('api.required_error_message'),
            'mimes' => trans('api.invalid_hr_file_type') ?: 'الصيغ المتاحة للملفات هي: PDF, PNG, JPG, JPEG, DOC, DOCX',
            'max' => trans('api.file_too_large_10mb') ?: 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
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

    public static function getAdminDocuments(User $user)
    {
        $response = HrApiRepository::getAdminDocuments($user);
        return UtilsRepository::handleResponseApi($response);
    }

    public static function getDocumentDetails($id, User $user)
    {
        $response = HrApiRepository::getDocumentDetails($id, $user);
        return UtilsRepository::handleResponseApi($response);
    }
}
