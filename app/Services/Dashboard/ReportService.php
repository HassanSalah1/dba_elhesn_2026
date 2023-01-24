<?php

namespace App\Services\Dashboard;


use App\Repositories\Dashboard\ReportRepository;

class ReportService
{

    public static function getAdministrativeReportData(array $data)
    {
        return ReportRepository::getAdministrativeReportData($data);
    }
}
