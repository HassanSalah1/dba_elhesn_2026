<?php

namespace App\Http\Controllers\Dashboard\Clinic;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Clinic\ClinicService;
use Illuminate\Http\Request;

class ClinicTimeSlotsController extends Controller
{
    public function showTimeSlots(Request $request)
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.clinic_time_slots_title');
        $data['debatable_names'] = [
            trans('admin.day_of_week'),
            trans('admin.start_time'),
            trans('admin.end_time'),
            trans('admin.max_bookings'),
            trans('admin.status'),
            trans('admin.actions'),
        ];
        return view('admin.clinic.time-slots.index')->with($data);
    }

    public function getTimeSlotsData(Request $request)
    {
        $data = $request->all();
        return ClinicService::getTimeSlotsData($data);
    }

    public function addTimeSlot(Request $request)
    {
        $data = $request->all();
        return ClinicService::addTimeSlot($data);
    }

    public function getTimeSlotData(Request $request)
    {
        $data = $request->all();
        return ClinicService::getTimeSlotData($data);
    }

    public function editTimeSlot(Request $request)
    {
        $data = $request->all();
        return ClinicService::editTimeSlot($data);
    }

    public function deleteTimeSlot(Request $request)
    {
        $data = $request->all();
        return ClinicService::deleteTimeSlot($data);
    }
}
