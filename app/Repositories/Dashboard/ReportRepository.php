<?php

namespace App\Repositories\Dashboard;

use App\Entities\UserRoles;
use App\Models\AdministrativeReport;
use App\Models\AdvanceRequest;
use Yajra\DataTables\Facades\DataTables;

class ReportRepository
{


    public static function getAdministrativeReportData(array $data)
    {
        $reports = AdministrativeReport::orderBy('id', 'DESC');

        return DataTables::of($reports)
            ->addColumn('team_name', function ($report) {
                return $report->user_team->full_team_name;
            })
            ->addColumn('employee_name', function ($report) {
                return $report->user->name;
            })
            ->make(true);
    }

    public static function getAdvanceRequestsData(array $data)
    {
        $reports = AdvanceRequest::orderBy('id', 'DESC');

        return DataTables::of($reports)
            ->addColumn('team_name', function ($report) {
                return $report->user_team->full_team_name;
            })
            ->addColumn('employee_name', function ($report) {
                return $report->user->name;
            })
            ->make(true);
    }
}
