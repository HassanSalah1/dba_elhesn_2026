<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Services\Api\V2\User\HrApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrController extends Controller
{
    public function login(Request $request)
    {
        return HrApiService::login($request->all());
    }

    public function getAttendance(Request $request)
    {
        $user = Auth::user();
        return HrApiService::getAttendance($request->all(), $user);
    }

    public function getLeaveTypes(Request $request)
    {
        return HrApiService::getLeaveTypes();
    }

    public function createLeaveRequest(Request $request)
    {
        $user = Auth::user();
        $attachment = $request->file('attachment');
        return HrApiService::createLeaveRequest($request->all(), $user, $attachment);
    }

    public function getLeaveRequests(Request $request)
    {
        $user = Auth::user();
        return HrApiService::getLeaveRequests($user);
    }

    public function getLeaveRequestDetails(Request $request, $id)
    {
        $user = Auth::user();
        return HrApiService::getLeaveRequestDetails($id, $user);
    }

    public function createDocument(Request $request)
    {
        $user = Auth::user();
        $attachment = $request->file('attachment');
        return HrApiService::createDocument($request->all(), $user, $attachment);
    }

    public function getDocuments(Request $request)
    {
        $user = Auth::user();
        return HrApiService::getDocuments($user);
    }

    public function getDocumentDetails(Request $request, $id)
    {
        $user = Auth::user();
        return HrApiService::getDocumentDetails($id, $user);
    }
}
