<?php

namespace App\Services\Dashboard;


use App\Repositories\Dashboard\ReportRepository;

class ReportService
{

    public static function getAdministrativeReportData(array $data)
    {
        return ReportRepository::getAdministrativeReportData($data);
    }

    public static function getAdvanceRequestsData(array $data)
    {
        return ReportRepository::getAdvanceRequestsData($data);
    }

    public static function getPresenceAbsenceData(array $data)
    {
        return ReportRepository::getPresenceAbsenceData($data);
    }
}
