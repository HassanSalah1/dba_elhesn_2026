<?php

namespace App\Repositories\Api\V2\User;

use App\Entities\HttpCode;
use App\Models\ClinicBooking;
use App\Models\ClinicBookingAttachment;
use App\Models\ClinicTimeSlot;
use App\Http\Resources\V2\ClinicBookingResource;
use App\Http\Resources\V2\ClinicTimeSlotResource;
use App\Http\Resources\V2\ClinicAttachmentResource;
use Carbon\Carbon;

class ClinicApiRepository
{
    public static function getTimeSlots(array $data)
    {
        $date = isset($data['date']) ? $data['date'] : date('Y-m-d');
        
        try {
            $dayOfWeek = Carbon::parse($date)->format('l');
        } catch (\Exception $e) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => HttpCode::ERROR
            ];
        }

        $timeSlots = ClinicTimeSlot::where('day_of_week', $dayOfWeek)
            ->where('status', 1)
            ->orderBy('start_time', 'asc')
            ->get();

        foreach ($timeSlots as $slot) {
            $bookedCount = ClinicBooking::where('booking_date', $date)
                ->where('time_slot_id', $slot->id)
                ->where('status', '!=', ClinicBooking::STATUS_CANCELLED)
                ->count();
                
            $slot->is_available = $bookedCount < $slot->max_bookings;
        }

        // Return all slots with their availability status
        return [
            'data' => ClinicTimeSlotResource::collection($timeSlots),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function createBooking(array $data)
    {
        $date = $data['booking_date'];
        $timeSlotId = $data['time_slot_id'];

        $slot = ClinicTimeSlot::where('id', $timeSlotId)->where('status', 1)->first();
        if (!$slot) {
            return [
                'message' => trans('api.time_slot_not_available'),
                'code' => HttpCode::ERROR
            ];
        }

        // Verify that the booking date's day of week matches the slot's day of week
        try {
            $dayOfWeek = Carbon::parse($date)->format('l');
            if (strtolower($dayOfWeek) !== strtolower($slot->day_of_week)) {
                return [
                    'message' => trans('api.time_slot_not_available'),
                    'code' => HttpCode::ERROR
                ];
            }
        } catch (\Exception $e) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => HttpCode::ERROR
            ];
        }

        $bookedCount = ClinicBooking::where('booking_date', $date)
            ->where('time_slot_id', $timeSlotId)
            ->where('status', '!=', ClinicBooking::STATUS_CANCELLED)
            ->count();

        if ($bookedCount >= $slot->max_bookings) {
            return [
                'message' => trans('api.time_slot_not_available'),
                'code' => HttpCode::ERROR
            ];
        }

        $booking = ClinicBooking::create([
            'user_id' => auth()->id(),
            'time_slot_id' => $timeSlotId,
            'booking_date' => $date,
            'is_for_other' => $data['is_for_other'],
            'other_name' => $data['is_for_other'] ? $data['other_name'] : null,
            'other_phone' => $data['is_for_other'] ? $data['other_phone'] : null,
            'other_country_code' => $data['is_for_other'] ? (isset($data['other_country_code']) ? $data['other_country_code'] : null) : null,
            'injury_type' => isset($data['injury_type']) ? $data['injury_type'] : null,
            'description' => isset($data['description']) ? $data['description'] : null,
            'status' => ClinicBooking::STATUS_PENDING,
        ]);

        if ($booking && $data['request']->hasFile('attachments')) {
            $files = $data['request']->file('attachments');
            if (!is_array($files)) {
                $files = [$files];
            }
            $filePath = 'uploads/clinic/';
            if (!file_exists(public_path($filePath))) {
                mkdir(public_path($filePath), 0755, true);
            }

            foreach ($files as $file) {
                $file_id = 'file_' . mt_rand(10000, 99999) . (time() + mt_rand(10000, 99999));
                $origName = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $newFileName = $file_id . '.' . $ext;
                $fileSize = $file->getSize();

                if ($file->move(public_path($filePath), $newFileName)) {
                    ClinicBookingAttachment::create([
                        'booking_id' => $booking->id,
                        'file_name' => $origName,
                        'file_path' => $filePath . $newFileName,
                        'file_type' => strtolower($ext),
                        'file_size' => $fileSize,
                    ]);
                }
            }
        }

        if ($booking) {
            return [
                'data' => new ClinicBookingResource($booking->load('attachments', 'timeSlot')),
                'message' => trans('api.booking_created'),
                'code' => HttpCode::SUCCESS
            ];
        }

        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function getBookings(array $data)
    {
        $status = isset($data['status']) ? $data['status'] : null;
        $query = ClinicBooking::where('user_id', auth()->id())->with('timeSlot', 'attachments');

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'cancelled') {
            $query->cancelled();
        } elseif ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->orderBy('id', 'desc')->get();

        return [
            'data' => ClinicBookingResource::collection($bookings),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getBookingDetails(array $data)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->where('user_id', auth()->id())
            ->with('timeSlot', 'attachments')
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        return [
            'data' => new ClinicBookingResource($booking),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function cancelBooking(array $data)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        if (in_array($booking->status, [ClinicBooking::STATUS_COMPLETED, ClinicBooking::STATUS_CANCELLED])) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => HttpCode::ERROR
            ];
        }

        $booking->update([
            'status' => ClinicBooking::STATUS_CANCELLED
        ]);

        return [
            'message' => trans('api.booking_cancelled'),
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function addAttachment(array $data)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        $file = $data['request']->file('file');
        $filePath = 'uploads/clinic/';
        if (!file_exists(public_path($filePath))) {
            mkdir(public_path($filePath), 0755, true);
        }

        $file_id = 'file_' . mt_rand(10000, 99999) . (time() + mt_rand(10000, 99999));
        $origName = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $newFileName = $file_id . '.' . $ext;
        $fileSize = $file->getSize();

        if ($file->move(public_path($filePath), $newFileName)) {
            $attachment = ClinicBookingAttachment::create([
                'booking_id' => $booking->id,
                'file_name' => $origName,
                'file_path' => $filePath . $newFileName,
                'file_type' => strtolower($ext),
                'file_size' => $fileSize,
            ]);

            return [
                'data' => new ClinicAttachmentResource($attachment),
                'message' => trans('api.attachment_uploaded'),
                'code' => HttpCode::SUCCESS
            ];
        }

        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function deleteAttachment(array $data)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        $attachment = ClinicBookingAttachment::where('id', $data['attachment_id'])
            ->where('booking_id', $booking->id)
            ->first();

        if (!$attachment) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => HttpCode::ERROR
            ];
        }

        if (file_exists(public_path($attachment->file_path))) {
            @unlink(public_path($attachment->file_path));
        }

        $attachment->delete();

        return [
            'message' => trans('api.attachment_deleted'),
            'code' => HttpCode::SUCCESS
        ];
    }
}
