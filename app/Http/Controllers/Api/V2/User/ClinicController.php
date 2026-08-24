<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Services\Api\V2\User\ClinicApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicController extends Controller
{
    public function getTimeSlots(Request $request)
    {
        $data = $request->all();
        return ClinicApiService::getTimeSlots($data);
    }

    public function createBooking(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        $user = Auth::user();
        return ClinicApiService::createBooking($data, $user);
    }

    public function getBookings(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        return ClinicApiService::getBookings($data, $user);
    }

    public function getAdminBookings(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        return ClinicApiService::getAdminBookings($data, $user);
    }

    public function getBookingDetails(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        $user = Auth::user();
        return ClinicApiService::getBookingDetails($data, $user);
    }

    public function cancelBooking(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        $user = Auth::user();
        return ClinicApiService::cancelBooking($data, $user);
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        $user = Auth::user();
        return ClinicApiService::updateBookingStatus($data, $user);
    }

    public function addAttachment(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        $data['request'] = $request;
        $user = Auth::user();
        return ClinicApiService::addAttachment($data, $user);
    }

    public function deleteAttachment(Request $request, $id, $attachmentId)
    {
        $data = $request->all();
        $data['id'] = $id;
        $data['attachment_id'] = $attachmentId;
        $user = Auth::user();
        return ClinicApiService::deleteAttachment($data, $user);
    }
}
