<?php

namespace App\Repositories\Dashboard\Clinic;

use App\Models\ClinicBooking;
use App\Models\ClinicTimeSlot;
use Yajra\DataTables\Facades\DataTables;

class ClinicRepository
{
    public static function getBookingsData(array $data)
    {
        $bookings = ClinicBooking::with(['user', 'timeSlot'])->orderBy('id', 'DESC');
        
        return DataTables::of($bookings)
            ->addColumn('user_name', function ($booking) {
                if ($booking->is_for_other) {
                    return $booking->other_name . ' (' . trans('api.booking_for_other') . ' - ' . ($booking->user ? $booking->user->name : '') . ')';
                }
                return $booking->user ? $booking->user->name : '';
            })
            ->addColumn('booking_time', function ($booking) {
                return $booking->timeSlot ? $booking->timeSlot->start_time . ' - ' . $booking->timeSlot->end_time : '';
            })
            ->editColumn('status', function ($booking) {
                $badgeClass = 'badge-light-warning';
                if ($booking->status === 'confirmed') $badgeClass = 'badge-light-primary';
                if ($booking->status === 'completed') $badgeClass = 'badge-light-success';
                if ($booking->status === 'cancelled') $badgeClass = 'badge-light-danger';
                return '<span class="badge rounded-pill ' . $badgeClass . '">' . trans('api.booking_status_' . $booking->status) . '</span>';
            })
            ->addColumn('actions', function ($booking) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.show') . '" id="' . $booking->id . '" href="' . url('/admin/clinic/booking/' . $booking->id) . '" class="on-default edit-row btn btn-info btn-sm"><i data-feather="eye"></i></a>';
                return $ul;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    public static function getTimeSlotsData(array $data)
    {
        $timeSlots = ClinicTimeSlot::orderBy('day_of_week', 'asc')->orderBy('start_time', 'asc');
        
        return DataTables::of($timeSlots)
            ->editColumn('day_of_week', function ($slot) {
                $days = [
                    'Saturday' => 'السبت',
                    'Sunday' => 'الأحد',
                    'Monday' => 'الإثنين',
                    'Tuesday' => 'الثلاثاء',
                    'Wednesday' => 'الأربعاء',
                    'Thursday' => 'الخميس',
                    'Friday' => 'الجمعة',
                ];
                return isset($days[$slot->day_of_week]) ? $days[$slot->day_of_week] : $slot->day_of_week;
            })
            ->editColumn('start_time', function ($slot) {
                return date('H:i', strtotime($slot->start_time));
            })
            ->editColumn('end_time', function ($slot) {
                return date('H:i', strtotime($slot->end_time));
            })
            ->editColumn('status', function ($slot) {
                $badgeClass = $slot->status ? 'badge-light-success' : 'badge-light-danger';
                $text = $slot->status ? trans('admin.active_status') : trans('admin.blocked_status');
                return '<span class="badge rounded-pill ' . $badgeClass . '">' . $text . '</span>';
            })
            ->addColumn('actions', function ($slot) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $slot->id . '" onclick="editSlot(this);return false;" href="#" class="on-default edit-row btn btn-info btn-sm"><i data-feather="edit"></i></a> ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $slot->id . '" onclick="deleteSlot(this);return false;" href="#" class="on-default remove-row btn btn-danger btn-sm"><i data-feather="trash-2"></i></a>';
                return $ul;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }
}
