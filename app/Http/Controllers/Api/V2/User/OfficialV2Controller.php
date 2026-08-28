<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Services\Api\V2\User\OfficialApiService;
use Illuminate\Http\Request;

class OfficialV2Controller extends Controller
{
    /**
     * Get attendance reasons
     */
    public function getAttendanceReasons(Request $request)
    {
        return OfficialApiService::getAttendanceReasons();
    }

    /**
     * Record player attendance (single or bulk)
     */
    public function recordAttendance(Request $request)
    {
        $data = $request->all();
        return OfficialApiService::recordAttendance($data);
    }

    /**
     * Create administrative report
     */
    public function createAdministrativeReport(Request $request)
    {
        $data = $request->all();
        return OfficialApiService::createAdministrativeReport($data);
    }

    /**
     * Get administrative reports
     */
    public function getAdministrativeReports(Request $request)
    {
        $data = $request->all();
        return OfficialApiService::getAdministrativeReports($data);
    }

    /**
     * Create advance request
     */
    public function createAdvanceRequest(Request $request)
    {
        $data = $request->all();
        return OfficialApiService::createAdvanceRequest($data);
    }

    /**
     * Get advance requests
     */
    public function getAdvanceRequests(Request $request)
    {
        $data = $request->all();
        return OfficialApiService::getAdvanceRequests($data);
    }

    /**
     * Get teams assigned to official
     */
    public function getTeams(Request $request)
    {
        $data = $request->all();
        return OfficialApiService::getTeams($data);
    }

    /**
     * Get players belonging to a team
     */
    public function getTeamPlayers(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return OfficialApiService::getTeamPlayers($data);
    }

    /**
     * Get Today's Events / Calendar (Mocked)
     */
    public function getEvents(Request $request)
    {
        $data = $request->all();
        return OfficialApiService::getEvents($data);
    }

    /**
     * Get comprehensive Player Profile (Mocked)
     */
    public function getPlayerProfile(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return OfficialApiService::getPlayerProfile($data);
    }

    /**
     * Get Coach Instructions for a player (Mocked)
     */
    public function getCoachInstructions(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return OfficialApiService::getCoachInstructions($data);
    }
}
