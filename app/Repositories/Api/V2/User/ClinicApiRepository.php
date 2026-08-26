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
        $today = Carbon::now('Asia/Dubai')->format('Y-m-d');
        $currentTime = Carbon::now('Asia/Dubai')->format('H:i:s');
        $isDateProvided = !empty($data['date']);
        $requestedDate = $isDateProvided ? $data['date'] : $today;

        try {
            $requestedCarbon = Carbon::parse($requestedDate);
            $requestedDate = $requestedCarbon->format('Y-m-d');
        } catch (\Exception $e) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => HttpCode::ERROR
            ];
        }

        // If no date was provided, or the date is today or in the past (current day / initial screen load)
        if (!$isDateProvided || $requestedDate <= $today) {
            $targetCarbon = Carbon::parse($today);
            $targetDate = $today;
            $timeSlots = collect();

            // Search starting from today for up to 30 days until a day with available slots is found
            for ($i = 0; $i < 30; $i++) {
                $checkDate = $targetCarbon->format('Y-m-d');
                $slots = self::getTimeSlotsForDate($checkDate, $today, $currentTime);

                if ($slots->contains('is_available', true)) {
                    $timeSlots = $slots;
                    $targetDate = $checkDate;
                    break;
                }

                $targetCarbon->addDay();
            }

            // Fallback: if no available slots found in 30 days, load today's slots
            if ($timeSlots->isEmpty()) {
                $timeSlots = self::getTimeSlotsForDate($today, $today, $currentTime);
                $targetDate = $today;
            }
        } else {
            // A specific future date was requested
            $timeSlots = self::getTimeSlotsForDate($requestedDate, $today, $currentTime);
            $targetDate = $requestedDate;
        }

        return [
            'data' => ClinicTimeSlotResource::collection($timeSlots),
            'date' => $targetDate,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getTimeSlotsForDate($date, $today, $currentTime)
    {
        try {
            $dayOfWeek = Carbon::parse($date)->format('l');
        } catch (\Exception $e) {
            return collect();
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

            $isPast = ($date < $today) || ($date == $today && $slot->start_time <= $currentTime);
            $remainingBookings = max(0, $slot->max_bookings - $bookedCount);

            $slot->is_available = !$isPast && ($remainingBookings > 0);
            $slot->max_bookings = $remainingBookings;
            $slot->date = $date;
        }

        return $timeSlots;
    }

    public static function createBooking(array $data, $user = null)
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

        $today = Carbon::now('Asia/Dubai')->format('Y-m-d');
        $currentTime = Carbon::now('Asia/Dubai')->format('H:i:s');
        if ($date < $today || ($date == $today && $slot->start_time <= $currentTime)) {
            return [
                'message' => trans('api.time_slot_not_available'),
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

        $userId = $user ? $user->id : (isset($data['user_id']) ? $data['user_id'] : null);
        $patientName = !empty($data['patient_name']) ? $data['patient_name'] : ($user ? $user->name : '');
        $patientPhone = !empty($data['patient_phone']) ? $data['patient_phone'] : ($user ? $user->phone : '');

        $booking = ClinicBooking::create([
            'user_id'            => $userId,
            'patient_name'       => $patientName,
            'patient_phone'      => $patientPhone,
            'time_slot_id'       => $timeSlotId,
            'booking_date'       => $date,
            'is_for_other'       => isset($data['is_for_other']) ? (bool)$data['is_for_other'] : false,
            'other_name'         => (isset($data['is_for_other']) && $data['is_for_other']) ? $data['other_name'] : null,
            'other_phone'        => (isset($data['is_for_other']) && $data['is_for_other']) ? $data['other_phone'] : null,
            'other_country_code' => (isset($data['is_for_other']) && $data['is_for_other']) ? (isset($data['other_country_code']) ? $data['other_country_code'] : null) : null,
            'description'        => isset($data['description']) ? $data['description'] : null,
            'status'             => ClinicBooking::STATUS_PENDING,
        ]);

        if ($booking && isset($data['request']) && $data['request']->hasFile('attachments')) {
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
            // Real-time sync to SQL Server (silent fail — cron job will retry)
            try {
                \App\Repositories\Api\V2\SqlServerApiRepository::pushSingleBookingToSqlServer($booking);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Real-time clinic booking sync failed for ID: ' . $booking->id, [
                    'error' => $e->getMessage()
                ]);
            }

            return [
                'data' => new ClinicBookingResource($booking->load('attachments', 'timeSlot', 'user')),
                'message' => trans('api.booking_created'),
                'code' => HttpCode::SUCCESS
            ];
        }

        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function getBookings(array $data, $user = null)
    {
        $status = isset($data['status']) ? $data['status'] : null;
        $query = ClinicBooking::with('timeSlot', 'attachments', 'user');

        if ($user) {
            // Only show bookings of the authenticated user
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if (!empty($user->phone)) {
                    $q->orWhere('patient_phone', $user->phone);
                }
            });
        } elseif (isset($data['patient_phone']) && !empty($data['patient_phone'])) {
            $query->where('patient_phone', $data['patient_phone']);
        }

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

    public static function getAdminBookings(array $data, $user)
    {
        if (!$user || !$user->isMedical()) {
            return [
                'message' => trans('api.unauthorized') ?: 'غير مصرح لك بإجراء هذه العملية',
                'code'    => HttpCode::ERROR
            ];
        }

        $query = ClinicBooking::with('timeSlot', 'attachments', 'user');

        // Optional filters for Medical Admin
        if (isset($data['status']) && !empty($data['status'])) {
            if ($data['status'] === 'active') {
                $query->active();
            } elseif ($data['status'] === 'cancelled') {
                $query->cancelled();
            } else {
                $query->where('status', $data['status']);
            }
        }

        if (isset($data['date']) && !empty($data['date'])) {
            $query->where('booking_date', $data['date']);
        }

        if (isset($data['search']) && !empty($data['search'])) {
            $search = $data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'LIKE', "%{$search}%")
                    ->orWhere('patient_phone', 'LIKE', "%{$search}%")
                    ->orWhere('other_name', 'LIKE', "%{$search}%")
                    ->orWhere('other_phone', 'LIKE', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')->orderBy('id', 'desc')->get();

        return [
            'data'    => ClinicBookingResource::collection($bookings),
            'message' => 'success',
            'code'    => HttpCode::SUCCESS
        ];
    }

    public static function getBookingDetails(array $data, $user = null)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->with('timeSlot', 'attachments', 'user')
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        // Authorization check: allow owner or medical admin
        if ($user && !$user->isMedical()) {
            $isOwner = ($booking->user_id && $booking->user_id == $user->id) ||
                       (!empty($user->phone) && $booking->patient_phone == $user->phone);
            if (!$isOwner) {
                return [
                    'message' => trans('api.unauthorized') ?: 'غير مصرح لك بعرض هذا الحجز',
                    'code'    => HttpCode::ERROR
                ];
            }
        }

        return [
            'data' => new ClinicBookingResource($booking),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function cancelBooking(array $data, $user = null)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        // Authorization check: allow owner or medical admin
        if ($user && !$user->isMedical()) {
            $isOwner = ($booking->user_id && $booking->user_id == $user->id) ||
                       (!empty($user->phone) && $booking->patient_phone == $user->phone);
            if (!$isOwner) {
                return [
                    'message' => trans('api.unauthorized') ?: 'غير مصرح لك بإلغاء هذا الحجز',
                    'code'    => HttpCode::ERROR
                ];
            }
        }

        if (in_array($booking->status, [ClinicBooking::STATUS_COMPLETED, ClinicBooking::STATUS_CANCELLED])) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => HttpCode::ERROR
            ];
        }

        $booking->update([
            'status' => ClinicBooking::STATUS_CANCELLED,
            'synced_to_sqlserver' => false,
        ]);

        return [
            'message' => trans('api.booking_cancelled'),
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function updateBookingStatus(array $data, $user)
    {
        if (!$user || !$user->isMedical()) {
            return [
                'message' => trans('api.unauthorized') ?: 'غير مصرح لك بإجراء هذه العملية',
                'code'    => HttpCode::ERROR
            ];
        }

        $booking = ClinicBooking::where('id', $data['id'])->with('timeSlot', 'attachments', 'user')->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code'    => HttpCode::ERROR
            ];
        }

        $status = $data['status'];
        if (!in_array($status, [ClinicBooking::STATUS_PENDING, ClinicBooking::STATUS_CONFIRMED, ClinicBooking::STATUS_COMPLETED, ClinicBooking::STATUS_CANCELLED])) {
            return [
                'message' => 'حالة الحجز غير صحيحة',
                'code'    => HttpCode::ERROR
            ];
        }

        $booking->update([
            'status'              => $status,
            'synced_to_sqlserver' => false,
        ]);

        return [
            'data'    => new ClinicBookingResource($booking),
            'message' => 'تم تحديث حالة الحجز بنجاح',
            'code'    => HttpCode::SUCCESS
        ];
    }

    public static function addAttachment(array $data, $user = null)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        // Authorization check: allow owner or medical admin
        if ($user && !$user->isMedical()) {
            $isOwner = ($booking->user_id && $booking->user_id == $user->id) ||
                       (!empty($user->phone) && $booking->patient_phone == $user->phone);
            if (!$isOwner) {
                return [
                    'message' => trans('api.unauthorized') ?: 'غير مصرح لك بتعديل هذا الحجز',
                    'code'    => HttpCode::ERROR
                ];
            }
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

    public static function deleteAttachment(array $data, $user = null)
    {
        $booking = ClinicBooking::where('id', $data['id'])
            ->first();

        if (!$booking) {
            return [
                'message' => trans('api.booking_not_found'),
                'code' => HttpCode::ERROR
            ];
        }

        // Authorization check: allow owner or medical admin
        if ($user && !$user->isMedical()) {
            $isOwner = ($booking->user_id && $booking->user_id == $user->id) ||
                       (!empty($user->phone) && $booking->patient_phone == $user->phone);
            if (!$isOwner) {
                return [
                    'message' => trans('api.unauthorized') ?: 'غير مصرح لك بحذف مرفق من هذا الحجز',
                    'code'    => HttpCode::ERROR
                ];
            }
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
