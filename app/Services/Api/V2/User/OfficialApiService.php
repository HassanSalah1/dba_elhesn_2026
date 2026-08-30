<?php

namespace App\Services\Api\V2\User;

use App\Entities\HttpCode;
use App\Repositories\Api\V2\User\OfficialApiRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class OfficialApiService
{
    /**
     * Get attendance reasons
     */
    public static function getAttendanceReasons()
    {
        $response = OfficialApiRepository::getAttendanceReasons();
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Record attendance (single or bulk)
     */
    public static function recordAttendance(array $data)
    {
        $keys = [
            'team_id' => 'required',
            'date'    => 'required',
            'players' => 'required|array',
        ];

        $messages = [
            'required'      => trans('api.required_error_message'),
            'players.array' => 'Players must be an array of player objects',
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = OfficialApiRepository::recordAttendance($data);
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Create administrative report
     */
    public static function createAdministrativeReport(array $data)
    {
        $keys = [
            'team_id' => 'required',
            'date'    => 'required',
            'subject' => 'required',
        ];

        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = OfficialApiRepository::createAdministrativeReport($data);
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Get administrative reports
     */
    public static function getAdministrativeReports(array $data)
    {
        $response = OfficialApiRepository::getAdministrativeReports($data);
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Create advance request
     */
    public static function createAdvanceRequest(array $data)
    {
        $keys = [
            'team_id' => 'required',
            'cost'    => 'required',
        ];

        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = OfficialApiRepository::createAdvanceRequest($data);
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Get advance requests
     */
    public static function getAdvanceRequests(array $data)
    {
        $response = OfficialApiRepository::getAdvanceRequests($data);
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Get teams assigned to current official
     */
    public static function getTeams(array $data)
    {
        $response = OfficialApiRepository::getTeams($data);
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Get players of a team
     */
    public static function getTeamPlayers(array $data)
    {
        $keys = [
            'id' => 'required',
        ];

        $messages = [
            'required' => trans('api.required_error_message'),
        ];

        $validated = ValidationRepository::validateAPIGeneral($data, $keys, $messages);
        if ($validated !== true) {
            return $validated;
        }

        $response = OfficialApiRepository::getTeamPlayers($data);
        return UtilsRepository::handleResponseApi($response);
    }

    /**
     * Get Today's Events / Calendar (Mocked with date filter)
     */
    public static function getEvents(array $data)
    {
        // Filter by date if provided, otherwise default to today
        $targetDate = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : date('Y-m-d');

        // TODO: Replace with real database queries once Views are provided by Eng. Karim
        $mockEvents = [
            [
                'id'       => 1,
                'title'    => 'تدريب صباحي',
                'type'     => 'تدريب',
                'time'     => '10:00 ص',
                'location' => 'ملعب الصالة الرئيسي',
                'date'     => $targetDate,
            ],
            [
                'id'       => 2,
                'title'    => 'محاضرة فنية وتكتيكية',
                'type'     => 'اجتماع',
                'time'     => '04:30 م',
                'location' => 'قاعة المحاضرات',
                'date'     => $targetDate,
            ],
            [
                'id'       => 3,
                'title'    => 'تدريب مسائي وإحماء',
                'type'     => 'تدريب',
                'time'     => '06:00 م',
                'location' => 'الملعب الفرعي 1',
                'date'     => $targetDate,
            ],
        ];

        return UtilsRepository::handleResponseApi([
            'code'    => HttpCode::SUCCESS,
            'message' => 'success',
            'data'    => $mockEvents,
        ]);
    }

    /**
     * Get comprehensive Player Profile (Mocked)
     */
    public static function getPlayerProfile(array $data)
    {
        $keys = ['id' => 'required'];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, ['required' => trans('api.required_error_message')]);
        if ($validated !== true) return $validated;

        // TODO: Replace with real database queries once Views are provided by Eng. Karim
        $mockProfile = [
            'id' => $data['id'],
            'name' => 'احمد الدوسري',
            'team' => 'فريق كرة قدم - U13',
            'age' => 12,
            'evaluations' => [
                'physical' => 95,
                'skill' => 95,
                'mental' => 95
            ],
            'development_plan' => [
                'goal' => 'يتطلب هذا الهدف التركيز على تمارين التحمل عالي الكثافة (HIIT) وتطوير مهارات الركض السريع.',
                'status' => 'مكتمل',
                'delivery_date' => '24-08-2026'
            ],
            'attendance_ratio' => 45,
            'documents' => [
                ['title' => 'شهادة الميلاد', 'expiry' => '31-12-2030', 'status' => 'valid'],
                ['title' => 'الفحص الطبي', 'expiry' => '15-06-2026', 'status' => 'expiring'],
                ['title' => 'التأمين الصحي', 'expiry' => '20-01-2027', 'status' => 'valid']
            ],
            'today_task' => [
                'title' => 'تدريب اليوم',
                'time' => '07:00 مسائي',
                'location' => 'صالة الوحدة, الملعب 1'
            ],
            'medical_record' => [
                'injury' => 'إصابة الكاحل',
                'sessions' => '3/5',
                'next_session' => '24-04-2026',
                'status' => 'مكتمل'
            ]
        ];

        return UtilsRepository::handleResponseApi([
            'code'    => HttpCode::SUCCESS,
            'message' => 'success',
            'data'    => $mockProfile,
        ]);
    }

    /**
     * Get Coach Instructions for a player (Mocked)
     */
    public static function getCoachInstructions(array $data)
    {
        $keys = ['id' => 'required'];
        $validated = ValidationRepository::validateAPIGeneral($data, $keys, ['required' => trans('api.required_error_message')]);
        if ($validated !== true) return $validated;

        // TODO: Replace with real database queries once Views are provided by Eng. Karim
        $mockInstructions = [
            [
                'id' => 1,
                'instruction' => 'تواصل ولي الأمر لإبلاغنا بغياب اللاعب بسبب ارتباطه بامتحانات منتصف الفصل الدراسي، سيعود للتدريبات يوم الأحد القادم بتاريخ 20-01-2026',
                'date' => '2026-01-20'
            ]
        ];

        return UtilsRepository::handleResponseApi([
            'code'    => HttpCode::SUCCESS,
            'message' => 'success',
            'data'    => $mockInstructions,
        ]);
    }
}
