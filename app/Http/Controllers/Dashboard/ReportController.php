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

}
