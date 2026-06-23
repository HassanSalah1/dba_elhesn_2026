<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Services\Api\V2\User\ClinicApiService;
use Illuminate\Http\Request;

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
        return ClinicApiService::createBooking($data);
    }

    public function getBookings(Request $request)
    {
        $data = $request->all();
        return ClinicApiService::getBookings($data);
    }

    public function getBookingDetails(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return ClinicApiService::getBookingDetails($data);
    }

    public function cancelBooking(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return ClinicApiService::cancelBooking($data);
    }

    public function addAttachment(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        $data['request'] = $request;
        return ClinicApiService::addAttachment($data);
    }

    public function deleteAttachment(Request $request, $id, $attachmentId)
    {
        $data = $request->all();
        $data['id'] = $id;
        $data['attachment_id'] = $attachmentId;
        return ClinicApiService::deleteAttachment($data);
    }
}
