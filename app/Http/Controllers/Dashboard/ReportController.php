<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\Dashboard\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function showAdministrativeReport()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.administrative_report_title');
        $data['debatable_names'] = array(trans('admin.employee_name'), trans('admin.team'),
            trans('admin.date'), trans('admin.subject'), trans('admin.events'),
            trans('admin.pros'), trans('admin.cons'), trans('admin.recommendations'),
            trans('admin.location'));

        return view('admin.report.administrative_report')->with($data);
    }

    public function getAdministrativeReportData(Request $request)
    {
        $data = $request->all();
        return ReportService::getAdministrativeReportData($data);
    }


    public function showAdvanceRequests()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.advance_requests_title');
        $data['debatable_names'] = array(trans('admin.employee_name'), trans('admin.team'),
            trans('admin.players_count'), trans('admin.escorts_count'), trans('admin.cost'),
            trans('admin.location'), trans('admin.statement'), trans('admin.tournament'),
            trans('admin.match_timing'), trans('admin.move_date'), trans('admin.return_date'),
            trans('admin.breakfast'), trans('admin.lunch'), trans('admin.dinner'),
            trans('admin.snacks'));

        return view('admin.report.advance_requests')->with($data);
    }

    public function getAdvanceRequestsData(Request $request)
    {
        $data = $request->all();
        return ReportService::getAdvanceRequestsData($data);
    }

    public function showPresenceAbsence()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.presence_absence_title');
        $data['debatable_names'] = array(trans('admin.employee_name'), trans('admin.team'),
            trans('admin.date'), trans('admin.period'),);

        return view('admin.report.presence_absence')->with($data);
    }

    public function getPresenceAbsenceData(Request $request)
    {
        $data = $request->all();
        return ReportService::getPresenceAbsenceData($data);
    }


}
