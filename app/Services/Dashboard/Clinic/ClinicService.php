<?php

namespace App\Services\Dashboard\Clinic;

use App\Models\ClinicBooking;
use App\Models\ClinicTimeSlot;
use App\Repositories\Dashboard\Clinic\ClinicRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class ClinicService
{
    public static function getBookingsData(array $data)
    {
        return ClinicRepository::getBookingsData($data);
    }

    public static function getTimeSlotsData(array $data)
    {
        return ClinicRepository::getTimeSlotsData($data);
    }

    public static function addTimeSlot(array $data)
    {
        $rules = [
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_bookings' => 'required|integer|min:1',
            'status' => 'required|in:0,1',
        ];

        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }

        $slot = ClinicTimeSlot::create([
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_bookings' => $data['max_bookings'],
            'status' => $data['status'],
        ]);

        return UtilsRepository::response($slot, trans('admin.process_success_message'), trans('admin.success_title'));
    }

    public static function getTimeSlotData(array $data)
    {
        $slot = ClinicTimeSlot::find($data['id']);
        if (!$slot) {
            return UtilsRepository::response(false, null, null, trans('admin.general_error_message'), trans('admin.error_title'));
        }
        return UtilsRepository::response($slot);
    }

    public static function editTimeSlot(array $data)
    {
        $rules = [
            'id' => 'required|integer',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'max_bookings' => 'required|integer|min:1',
            'status' => 'required|in:0,1',
        ];

        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }

        $slot = ClinicTimeSlot::find($data['id']);
        if (!$slot) {
            return UtilsRepository::response(false, null, null, trans('admin.general_error_message'), trans('admin.error_title'));
        }

        $slot->update([
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_bookings' => $data['max_bookings'],
            'status' => $data['status'],
        ]);

        return UtilsRepository::response(true, trans('admin.process_success_message'), trans('admin.success_title'));
    }

    public static function deleteTimeSlot(array $data)
    {
        $slot = ClinicTimeSlot::find($data['id']);
        if (!$slot) {
            return UtilsRepository::response(false, null, null, trans('admin.general_error_message'), trans('admin.error_title'));
        }

        $slot->delete();

        return UtilsRepository::response(true, trans('admin.process_success_message'), trans('admin.success_title'));
    }

    public static function changeStatus(array $data)
    {
        $rules = [
            'id' => 'required|integer',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ];

        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }

        $booking = ClinicBooking::find($data['id']);
        if (!$booking) {
            return UtilsRepository::response(false, null, null, trans('admin.general_error_message'), trans('admin.error_title'));
        }

        $booking->update([
            'status' => $data['status']
        ]);

        return UtilsRepository::response(true, trans('admin.process_success_message'), trans('admin.success_title'));
    }
}
