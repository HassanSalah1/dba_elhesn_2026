<?php

namespace App\Repositories\Dashboard;

use App\Entities\UserRoles;
use App\Models\AdministrativeReport;
use App\Models\AdvanceRequest;
use App\Models\PresenceAbsence;
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
                return $report->user_team ? $report->user_team->full_team_name : ($report->team_name ?? '');
            })
            ->addColumn('employee_name', function ($report) {
                return $report->user ? $report->user->name : '';
            })
            ->editColumn('breakfast', function ($report) {
                $count = $report->breakfast_count ?: intval($report->breakfast);
                $cost = floatval($report->breakfast_cost);
                return $cost > 0 ? "{$count} ({$cost} د.إ)" : $count;
            })
            ->editColumn('lunch', function ($report) {
                $count = $report->lunch_count ?: intval($report->lunch);
                $cost = floatval($report->lunch_cost);
                return $cost > 0 ? "{$count} ({$cost} د.إ)" : $count;
            })
            ->editColumn('dinner', function ($report) {
                $count = $report->dinner_count ?: intval($report->dinner);
                $cost = floatval($report->dinner_cost);
                return $cost > 0 ? "{$count} ({$cost} د.إ)" : $count;
            })
            ->editColumn('snacks', function ($report) {
                $count = $report->snack_count ?: intval($report->snacks);
                $cost = floatval($report->snack_cost);
                return $cost > 0 ? "{$count} ({$cost} د.إ)" : $count;
            })
            ->make(true);
    }

    public static function getPresenceAbsenceData(array $data)
    {
        $reports = PresenceAbsence::orderBy('id', 'DESC');

        return DataTables::of($reports)
            ->addColumn('team_name', function ($report) {
                return $report->user_team->full_team_name;
            })
            ->addColumn('employee_name', function ($report) {
                return $report->user->name;
            })
            ->editColumn('period', function ($report) {
                return trans('admin.'.$report->period);
            })
            ->make(true);
    }
}
