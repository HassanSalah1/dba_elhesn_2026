<?php

namespace App\Http\Controllers\Dashboard\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ClinicBooking;
use App\Services\Dashboard\Clinic\ClinicService;
use Illuminate\Http\Request;

class ClinicBookingController extends Controller
{
    public function showBookings(Request $request)
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.clinic_bookings_title');
        $data['debatable_names'] = [
            trans('admin.booking_number'),
            trans('admin.player_name'),
            trans('admin.booking_date'),
            trans('admin.booking_time'),
            trans('admin.booking_status'),
            trans('admin.actions'),
        ];
        return view('admin.clinic.bookings.index')->with($data);
    }

    public function getBookingsData(Request $request)
    {
        $data = $request->all();
        return ClinicService::getBookingsData($data);
    }

    public function showBooking(Request $request, $id)
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.clinic_bookings_title');
        $data['bookingObj'] = ClinicBooking::with(['user', 'timeSlot', 'attachments'])->find($id);
        
        if (!$data['bookingObj']) {
            abort(404);
        }
        
        return view('admin.clinic.bookings.show')->with($data);
    }

    public function changeStatus(Request $request)
    {
        $data = $request->all();
        return ClinicService::changeStatus($data);
    }
}
