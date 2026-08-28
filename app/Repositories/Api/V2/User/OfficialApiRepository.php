<?php

namespace App\Repositories\Api\V2\User;

use App\Entities\HttpCode;
use App\Entities\Period;
use App\Http\Resources\TeamPlayerResource;
use App\Http\Resources\UserTeamResource;
use App\Http\Resources\V2\Official\AdministrativeReportResource;
use App\Http\Resources\V2\Official\AdvanceRequestResource;
use App\Http\Resources\V2\Official\AttendanceReasonResource;
use App\Models\AdministrativeReport;
use App\Models\AdvanceRequest;
use App\Models\AttendReason;
use App\Models\SportTeam;
use App\Models\TeamPlayer;
use App\Models\UserTeam;
use App\Repositories\Api\V2\SqlServerApiRepository;
use App\Repositories\General\UtilsRepository;
use Illuminate\Support\Facades\Log;

class OfficialApiRepository
{
    /**
     * Get list of attendance reasons (12+ official reasons)
     */
    public static function getAttendanceReasons(): array
    {
        $reasons = AttendReason::orderBy('the_order', 'asc')->get();

        return [
            'data'    => AttendanceReasonResource::collection($reasons),
            'message' => 'success',
            'code'    => HttpCode::SUCCESS,
        ];
    }

    /**
     * Record presence/absence for one or multiple players (bulk)
     */
    public static function recordAttendance(array $data): array
    {
        $user = auth()->user();
        $conn = SqlServerApiRepository::startConnection();

        if (!$conn) {
            return [
                'message' => trans('api.general_error_message'),
                'code'    => HttpCode::ERROR,
            ];
        }

        if (empty($data['players']) || !is_array($data['players'])) {
            \sqlsrv_close($conn);
            return [
                'message' => 'No players provided',
                'code'    => HttpCode::ERROR,
            ];
        }

        // Determine visit text based on period (defaults to one period per day)
        $period = $data['period'] ?? Period::one_period_day;
        $visit = 'يوم فترة واحدة';
        if ($period === Period::evening_another_period) {
            $visit = 'مسائى فترة تانيه';
        } elseif ($period === Period::my_first_morning) {
            $visit = 'صباحى فترة اولى';
        }

        $execute = false;
        foreach ($data['players'] as $player) {
            $sql = "INSERT INTO FBall.dbo.tbl_Players_Attendance 
                    (SeasonTeamPlayerRowID, ReasonKey, TheDate, PlayerRowID, UserID, WhenInserted, Comments, Relief, Visit) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $seasonPlayerId = SqlServerApiRepository::getSeasonTeamPlayerId($conn, $player['player_id']);
            $params = [
                $seasonPlayerId,
                $player['attendance_status'] ?? 0,
                date('Y-m-d H:i:s', strtotime($data['date'])),
                $player['player_id'],
                $user->user_id ?: $user->id,
                date('Y-m-d H:i:s'),
                $player['notes'] ?? null,
                null,
                $visit,
            ];

            $stmt = \sqlsrv_prepare($conn, $sql, $params);
            $execute = $stmt && \sqlsrv_execute($stmt);
        }

        \sqlsrv_close($conn);

        if ($execute) {
            if (!empty($data['players'][0]['player_id'])) {
                $playerObj = TeamPlayer::where('player_id', $data['players'][0]['player_id'])->first();
                if ($playerObj && $playerObj->team && $playerObj->team->email) {
                    UtilsRepository::sendReportEmail(
                        'تقييم الحضور للاعبين: ' . ($playerObj->team->name ?? ''),
                        $playerObj->team->email
                    );
                }
            }

            return [
                'message' => trans('api.success_message'),
                'code'    => HttpCode::SUCCESS,
            ];
        }

        return [
            'message' => trans('api.general_error_message'),
            'code'    => HttpCode::ERROR,
        ];
    }

    /**
     * Create an Administrative Report
     */
    public static function createAdministrativeReport(array $data): array
    {
        $user = auth()->user();
        $userTeam = UserTeam::find($data['team_id']);

        $officialId = $userTeam ? $userTeam->official_id : 0;
        $formattedDate = date('Y-m-d', strtotime($data['date']));

        // 1. Save locally in MySQL
        $report = AdministrativeReport::create([
            'user_id'             => $user->id,
            'user_team_id'        => $data['team_id'],
            'official_id'         => $officialId,
            'date'                => $formattedDate,
            'subject'             => $data['subject'],
            'location'            => $data['location'] ?? null,
            'events'              => $data['events'] ?? null,
            'cons'                => $data['cons'] ?? null,
            'pros'                => $data['pros'] ?? null,
            'recommendations'     => $data['recommendations'] ?? null,
            'synced_to_sqlserver' => false,
        ]);

        // 2. Attempt immediate push to SQL Server
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "INSERT INTO FBall.dbo.tblOfficial_Actions 
                    (OfficialID, UserID, InsertedDateTime, Topic, ActionDate, ActionPlace, TheEvents, Negativity, Positivity, Recommendations) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $officialId,
                $user->user_id ?: $user->id,
                date('Y-m-d H:i:s'),
                $data['subject'],
                date('Y-m-d H:i:s', strtotime($data['date'])),
                $data['location'] ?? null,
                $data['events'] ?? null,
                $data['cons'] ?? null,
                $data['pros'] ?? null,
                $data['recommendations'] ?? null,
            ];

            $stmt = \sqlsrv_prepare($conn, $sql, $params);
            if ($stmt && \sqlsrv_execute($stmt)) {
                $report->update(['synced_to_sqlserver' => true]);
            }
            \sqlsrv_close($conn);
        }

        // 3. Email notification if configured
        if ($userTeam && $userTeam->team && $userTeam->team->email) {
            UtilsRepository::sendReportEmail('تقرير الإدارى: ' . $userTeam->team->name, $userTeam->team->email);
        }

        return [
            'data'    => new AdministrativeReportResource($report),
            'message' => trans('api.success_message'),
            'code'    => HttpCode::SUCCESS,
        ];
    }

    /**
     * Get submitted Administrative Reports for current official
     */
    public static function getAdministrativeReports(array $data): array
    {
        $user = auth()->user();

        // Get team IDs associated with this user
        $userTeamIds = UserTeam::where('user_id', $user->id)->pluck('id')->toArray();
        $officialIds = UserTeam::where('user_id', $user->id)->pluck('official_id')->filter()->toArray();

        $query = AdministrativeReport::with('user_team.team')
            ->where(function ($q) use ($user, $userTeamIds, $officialIds) {
                $q->where('user_id', $user->id);
                if (!empty($userTeamIds)) {
                    $q->orWhereIn('user_team_id', $userTeamIds);
                }
                if (!empty($officialIds)) {
                    $q->orWhereIn('official_id', $officialIds);
                }
            });

        if (!empty($data['team_id'])) {
            $query->where('user_team_id', $data['team_id']);
        }

        if (!empty($data['date'])) {
            $query->where('date', date('Y-m-d', strtotime($data['date'])));
        }

        $reports = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();

        return [
            'data'    => AdministrativeReportResource::collection($reports),
            'message' => 'success',
            'code'    => HttpCode::SUCCESS,
        ];
    }

    /**
     * Create an Advance Request (طلب سلفة)
     */
    public static function createAdvanceRequest(array $data): array
    {
        $user = auth()->user();
        $userTeam = UserTeam::find($data['team_id']);
        $teamRowId = $userTeam && $userTeam->team ? $userTeam->team->team_id : null;

        // Build human-readable details string
        $detailsParts = [];
        if (!empty($data['breakfast']) && intval($data['breakfast']) > 0) {
            $detailsParts[] = 'فطار (' . intval($data['breakfast']) . ')';
        }
        if (!empty($data['snacks']) && intval($data['snacks']) > 0) {
            $detailsParts[] = 'سناكس (' . intval($data['snacks']) . ')';
        }
        if (!empty($data['lunch']) && intval($data['lunch']) > 0) {
            $detailsParts[] = 'غداء (' . intval($data['lunch']) . ')';
        }
        if (!empty($data['dinner']) && intval($data['dinner']) > 0) {
            $detailsParts[] = 'عشاء (' . intval($data['dinner']) . ')';
        }
        $details = implode(' + ', $detailsParts);

        // 1. Save locally in MySQL
        $request = AdvanceRequest::create([
            'user_id'             => $user->id,
            'user_team_id'        => $data['team_id'],
            'team_row_id'         => $teamRowId,
            'players_count'       => $data['players_count'] ?? 0,
            'escorts_count'       => $data['escorts_count'] ?? 0,
            'cost'                => $data['cost'] ?? 0,
            'location'            => $data['location'] ?? null,
            'statement'           => $data['statement'] ?? $details,
            'details'             => $details,
            'tournament'          => $data['tournament'] ?? null,
            'match_timing'        => $data['match_timing'] ?? null,
            'move_date'           => !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : ($data['move_date'] ?? null),
            'return_date'         => $data['return_date'] ?? null,
            'breakfast'           => isset($data['breakfast']) ? (string)$data['breakfast'] : null,
            'lunch'               => isset($data['lunch']) ? (string)$data['lunch'] : null,
            'dinner'              => isset($data['dinner']) ? (string)$data['dinner'] : null,
            'snacks'              => isset($data['snacks']) ? (string)$data['snacks'] : null,
            'type'                => $data['type'] ?? 'سلفة',
            'status'              => 'pending',
            'synced_to_sqlserver' => false,
        ]);

        // 2. Attempt immediate push to SQL Server
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "INSERT INTO FBall.dbo.tbl_RequestRelease 
                    (TeamRowID, Players, Officials, TheCost, Details, WhoInsert, WhenInsert, Match, TheDate, Place, MatchTime, LeaveTime, ReturnTime, Type, BreakfastCount, BreakfastCost, LunchCount, LunchCost, DinnerCount, DinnerCost, SnackCount, SnackCost) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $moveDate = $request->move_date ? date('Y-m-d H:i:s', strtotime($request->move_date)) : null;

            $params = [
                $teamRowId,
                $request->players_count,
                $request->escorts_count,
                $request->cost,
                $details,
                $user->user_id ?: $user->id,
                date('Y-m-d H:i:s'),
                $request->tournament,
                $moveDate,
                $request->location,
                $request->match_timing,
                null,
                $request->return_date,
                $request->type,
                intval($request->breakfast),
                0,
                intval($request->lunch),
                0,
                intval($request->dinner),
                0,
                intval($request->snacks),
                0,
            ];

            $stmt = \sqlsrv_prepare($conn, $sql, $params);
            if ($stmt && \sqlsrv_execute($stmt)) {
                $request->update(['synced_to_sqlserver' => true]);
            }
            \sqlsrv_close($conn);
        }

        // 3. Email notification if configured
        if ($userTeam && $userTeam->team && $userTeam->team->email) {
            UtilsRepository::sendReportEmail('طلب سلفة: ' . $userTeam->team->name, $userTeam->team->email);
        }

        return [
            'data'    => new AdvanceRequestResource($request),
            'message' => trans('api.success_message'),
            'code'    => HttpCode::SUCCESS,
        ];
    }

    /**
     * Get submitted Advance Requests for current official
     */
    public static function getAdvanceRequests(array $data): array
    {
        $user = auth()->user();

        $userTeamIds = UserTeam::where('user_id', $user->id)->pluck('id')->toArray();
        $teamIds = UserTeam::where('user_id', $user->id)->pluck('team_id')->filter()->toArray();

        $query = AdvanceRequest::with('user_team.team')
            ->where(function ($q) use ($user, $userTeamIds, $teamIds) {
                $q->where('user_id', $user->id);
                if (!empty($userTeamIds)) {
                    $q->orWhereIn('user_team_id', $userTeamIds);
                }
                if (!empty($teamIds)) {
                    $q->orWhereIn('team_row_id', $teamIds);
                }
            });

        if (!empty($data['team_id'])) {
            $query->where(function($q) use ($data) {
                $q->where('user_team_id', $data['team_id'])
                  ->orWhere('team_row_id', $data['team_id']);
            });
        }

        $requests = $query->orderBy('id', 'desc')->get();

        return [
            'data'    => AdvanceRequestResource::collection($requests),
            'message' => 'success',
            'code'    => HttpCode::SUCCESS,
        ];
    }

    /**
     * Get teams assigned to current official
     */
    public static function getTeams(array $data): array
    {
        $user = auth()->user();
        $teams = UserTeam::where('user_id', $user->id)->with('team')->get();

        return [
            'data'    => UserTeamResource::collection($teams),
            'message' => 'success',
            'code'    => HttpCode::SUCCESS,
        ];
    }

    /**
     * Get players belonging to a team
     */
    public static function getTeamPlayers(array $data): array
    {
        $teamId = $data['id'] ?? null;

        // Support passing either user_team id or direct sport_team id
        $userTeam = UserTeam::find($teamId);
        $resolvedTeamId = $userTeam ? $userTeam->team_id : $teamId;

        $team = SportTeam::find($resolvedTeamId);
        $players = TeamPlayer::where('team_id', $team ? $team->team_id : $resolvedTeamId)->get();

        return [
            'data'    => array_values(collect(TeamPlayerResource::collection($players))->sortBy('name')->toArray()),
            'message' => 'success',
            'code'    => HttpCode::SUCCESS,
        ];
    }
}
