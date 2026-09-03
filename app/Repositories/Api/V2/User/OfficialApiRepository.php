<?php

namespace App\Repositories\Api\V2\User;

use App\Entities\HttpCode;
use App\Entities\Period;
use App\Http\Resources\TeamPlayerResource;
use App\Http\Resources\UserTeamResource;
use App\Http\Resources\V2\Official\AdministrativeReportResource;
use App\Http\Resources\V2\Official\AdvanceRequestResource;
use App\Http\Resources\V2\Official\AttendanceReasonResource;
use App\Http\Resources\V2\Official\OfficialTeamPlayerResource;
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
     * Record presence/absence for players (locked against editing once submitted)
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

        $targetDate = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : date('Y-m-d');
        $playerIds = array_values(array_filter(array_map(fn($p) => intval($p['player_id'] ?? 0), $data['players'])));

        if (empty($playerIds)) {
            \sqlsrv_close($conn);
            return [
                'message' => 'No valid players provided',
                'code'    => HttpCode::ERROR,
            ];
        }

        // 1. Check existing attendance records in SQL Server for these players on targetDate
        $placeholders = implode(',', array_fill(0, count($playerIds), '?'));
        $checkSql = "SELECT PlayerRowID FROM FBall.dbo.tbl_Players_Attendance 
                     WHERE PlayerRowID IN ($placeholders) AND CAST(TheDate AS DATE) = ?";
        $checkParams = array_merge($playerIds, [$targetDate]);
        $checkStmt = \sqlsrv_query($conn, $checkSql, $checkParams);

        $alreadyRecordedPlayerIds = [];
        if ($checkStmt) {
            while ($row = \sqlsrv_fetch_object($checkStmt)) {
                $alreadyRecordedPlayerIds[] = (int) $row->PlayerRowID;
            }
        }

        // If ALL players in the request are already recorded, reject to prevent editing!
        if (count($alreadyRecordedPlayerIds) >= count($playerIds)) {
            \sqlsrv_close($conn);
            return [
                'message' => 'تم تسجيل الحضور لهذا التاريخ مسبقاً، ولا يمكن التعديل عليه نهائياً.',
                'code'    => HttpCode::ERROR,
            ];
        }

        // Filter out already recorded players (lock existing records, only allow new players)
        $newPlayers = array_values(array_filter($data['players'], function ($p) use ($alreadyRecordedPlayerIds) {
            return !in_array(intval($p['player_id'] ?? 0), $alreadyRecordedPlayerIds);
        }));

        if (empty($newPlayers)) {
            \sqlsrv_close($conn);
            return [
                'message' => 'تم تسجيل الحضور لهذا التاريخ مسبقاً، ولا يمكن التعديل عليه نهائياً.',
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
        foreach ($newPlayers as $player) {
            $sql = "INSERT INTO FBall.dbo.tbl_Players_Attendance 
                    (SeasonTeamPlayerRowID, ReasonKey, TheDate, PlayerRowID, UserID, WhenInserted, Comments, Relief, Visit) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $seasonPlayerId = SqlServerApiRepository::getSeasonTeamPlayerId($conn, $player['player_id']);
            $params = [
                $seasonPlayerId,
                $player['attendance_status'] ?? 0,
                date('Y-m-d H:i:s', strtotime($targetDate)),
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
            if (!empty($newPlayers[0]['player_id'])) {
                $playerObj = TeamPlayer::where('player_id', $newPlayers[0]['player_id'])->first();
                if ($playerObj && $playerObj->team && $playerObj->team->email) {
                    UtilsRepository::sendReportEmail(
                        'تقييم الحضور للاعبين: ' . ($playerObj->team->name ?? ''),
                        $playerObj->team->email
                    );
                }
            }

            $successMsg = trans('api.success_message');
            if (!empty($alreadyRecordedPlayerIds)) {
                $successMsg = 'تم تسجيل حضور اللاعبين الجدد، وتم قفل وتخطي اللاعبين المسجلين مسبقاً.';
            }

            return [
                'message' => $successMsg,
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

        // 1. Resolve team and team_row_id
        $userTeam = UserTeam::with('team')->find($data['team_id'] ?? 0);
        if (!$userTeam) {
            $userTeam = UserTeam::with('team')->where('user_id', $user->id)
                ->where(function($q) use ($data) {
                    $q->where('id', $data['team_id'] ?? 0)
                      ->orWhere('team_id', $data['team_id'] ?? 0);
                })->first();
        }
        $teamRowId = $userTeam && $userTeam->team ? $userTeam->team->team_id : ($userTeam ? $userTeam->team_id : ($data['team_id'] ?? null));

        // 2. Meal counts and costs
        $breakfastCount = intval($data['breakfast_count'] ?? $data['breakfast'] ?? 0);
        $breakfastCost  = floatval($data['breakfast_cost'] ?? 0);
        $lunchCount     = intval($data['lunch_count'] ?? $data['lunch'] ?? 0);
        $lunchCost      = floatval($data['lunch_cost'] ?? 0);
        $dinnerCount    = intval($data['dinner_count'] ?? $data['dinner'] ?? 0);
        $dinnerCost     = floatval($data['dinner_cost'] ?? 0);
        $snackCount     = intval($data['snack_count'] ?? $data['snacks'] ?? 0);
        $snackCost      = floatval($data['snack_cost'] ?? 0);

        // Calculate total cost automatically from meal costs if provided
        $calculatedCost = $breakfastCost + $lunchCost + $dinnerCost + $snackCost;
        $totalCost = ($calculatedCost > 0) ? $calculatedCost : floatval($data['cost'] ?? 0);

        $leaveTime = $data['leave_time'] ?? $data['move_time'] ?? null;
        $moveDate  = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : ($data['move_date'] ?? null);

        // Build human-readable details string
        $detailsParts = [];
        if ($breakfastCount > 0) {
            $detailsParts[] = 'فطار (' . $breakfastCount . ($breakfastCost > 0 ? ' - ' . $breakfastCost . ' درهم' : '') . ')';
        }
        if ($lunchCount > 0) {
            $detailsParts[] = 'غداء (' . $lunchCount . ($lunchCost > 0 ? ' - ' . $lunchCost . ' درهم' : '') . ')';
        }
        if ($dinnerCount > 0) {
            $detailsParts[] = 'عشاء (' . $dinnerCount . ($dinnerCost > 0 ? ' - ' . $dinnerCost . ' درهم' : '') . ')';
        }
        if ($snackCount > 0) {
            $detailsParts[] = 'سناكس (' . $snackCount . ($snackCost > 0 ? ' - ' . $snackCost . ' درهم' : '') . ')';
        }
        $details = !empty($data['details']) ? $data['details'] : implode(' + ', $detailsParts);

        // 3. Save locally in MySQL
        $request = AdvanceRequest::create([
            'user_id'             => $user->id,
            'user_team_id'        => $userTeam ? $userTeam->id : ($data['team_id'] ?? 0),
            'team_row_id'         => $teamRowId,
            'team_name'           => $userTeam ? ($userTeam->full_team_name ?: ($userTeam->team ? $userTeam->team->name_ar : null)) : null,
            'players_count'       => $data['players_count'] ?? 0,
            'escorts_count'       => $data['escorts_count'] ?? 0,
            'cost'                => $totalCost,
            'location'            => $data['location'] ?? ($data['place'] ?? null),
            'statement'           => $data['statement'] ?? $details,
            'details'             => $details,
            'tournament'          => $data['tournament'] ?? ($data['match'] ?? null),
            'match_timing'        => $data['match_timing'] ?? ($data['match_time'] ?? null),
            'leave_time'          => $leaveTime,
            'move_date'           => $moveDate,
            'return_date'         => $data['return_date'] ?? ($data['return_time'] ?? null),
            'breakfast'           => (string)$breakfastCount,
            'breakfast_count'     => $breakfastCount,
            'breakfast_cost'      => $breakfastCost,
            'lunch'               => (string)$lunchCount,
            'lunch_count'         => $lunchCount,
            'lunch_cost'          => $lunchCost,
            'dinner'              => (string)$dinnerCount,
            'dinner_count'        => $dinnerCount,
            'dinner_cost'         => $dinnerCost,
            'snacks'              => (string)$snackCount,
            'snack_count'         => $snackCount,
            'snack_cost'          => $snackCost,
            'type'                => $data['type'] ?? 'سلفة',
            'status'              => 'pending',
            'synced_to_sqlserver' => false,
        ]);

        // 4. Attempt immediate push to SQL Server
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "INSERT INTO FBall.dbo.tbl_RequestRelease 
                    (TeamRowID, Players, Officials, TheCost, Details, WhoInsert, WhenInsert, Match, TheDate, Place, MatchTime, LeaveTime, ReturnTime, Type, BreakfastCount, BreakfastCost, LunchCount, LunchCost, DinnerCount, DinnerCost, SnackCount, SnackCost) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $sqlMoveDate = $request->move_date ? date('Y-m-d H:i:s', strtotime($request->move_date)) : null;

            $params = [
                $teamRowId,
                $request->players_count,
                $request->escorts_count,
                $request->cost,
                $details,
                $user->user_id ?: $user->id,
                date('Y-m-d H:i:s'),
                $request->tournament,
                $sqlMoveDate,
                $request->location,
                $request->match_timing,
                $leaveTime,
                $request->return_date,
                $request->type,
                $breakfastCount,
                $breakfastCost,
                $lunchCount,
                $lunchCost,
                $dinnerCount,
                $dinnerCost,
                $snackCount,
                $snackCost,
            ];

            $stmt = \sqlsrv_prepare($conn, $sql, $params);
            if ($stmt && \sqlsrv_execute($stmt)) {
                $request->update(['synced_to_sqlserver' => true]);
            }
            \sqlsrv_close($conn);
        }

        // 5. Email notification if configured
        if ($userTeam && $userTeam->team && $userTeam->team->email) {
            UtilsRepository::sendReportEmail('طلب سلفة: ' . $userTeam->team->name, $userTeam->team->email);
        }

        return [
            'data'    => new AdvanceRequestResource($request->load(['user_team.team', 'sport_team'])),
            'message' => trans('api.success_message'),
            'code'    => HttpCode::SUCCESS,
        ];
    }

    /**
     * Get submitted Advance Requests archive for current official
     * Displays all requests in DB for the official's assigned teams
     */
    public static function getAdvanceRequests(array $data): array
    {
        $user = auth()->user();

        // Retrieve all team associations for this user
        $userTeams = UserTeam::with('team')->where('user_id', $user->id)->get();
        $userTeamIds = $userTeams->pluck('id')->toArray();
        $sportTeamIds = $userTeams->pluck('team_id')->toArray();
        $sqlServerTeamRowIds = $userTeams->map(fn($ut) => $ut->team ? $ut->team->team_id : null)->filter()->toArray();

        $allTeamRowIds = array_values(array_unique(array_filter(array_merge($userTeamIds, $sportTeamIds, $sqlServerTeamRowIds))));

        $query = AdvanceRequest::with(['user_team.team', 'sport_team'])
            ->where(function ($q) use ($user, $userTeamIds, $allTeamRowIds) {
                $q->where('user_id', $user->id);
                if (!empty($userTeamIds)) {
                    $q->orWhereIn('user_team_id', $userTeamIds);
                }
                if (!empty($allTeamRowIds)) {
                    $q->orWhereIn('team_row_id', $allTeamRowIds);
                }
            });

        if (!empty($data['team_id'])) {
            $teamId = $data['team_id'];
            $query->where(function($q) use ($teamId) {
                $q->where('user_team_id', $teamId)
                  ->orWhere('team_row_id', $teamId);
            });
        }

        if (!empty($data['date'])) {
            $query->where('move_date', date('Y-m-d', strtotime($data['date'])));
        }

        $requests = $query->orderBy('move_date', 'desc')->orderBy('id', 'desc')->get();

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
     * Get players belonging to a team with their attendance status and lock indicator for a specific date
     */
    public static function getTeamPlayers(array $data): array
    {
        $teamId = $data['id'] ?? null;
        $targetDate = !empty($data['date']) ? date('Y-m-d', strtotime($data['date'])) : date('Y-m-d');

        // Support passing either user_team id or direct sport_team id
        $userTeam = UserTeam::find($teamId);
        $resolvedTeamId = $userTeam ? $userTeam->team_id : $teamId;

        $team = SportTeam::find($resolvedTeamId);
        $players = TeamPlayer::where('team_id', $team ? $team->team_id : $resolvedTeamId)->get();

        if ($players->isEmpty()) {
            return [
                'data'    => [],
                'message' => 'success',
                'code'    => HttpCode::SUCCESS,
            ];
        }

        // Cache attendance reasons map: [reason_key => reason]
        $reasonsMap = AttendReason::pluck('reason', 'reason_key')->toArray();
        if (!isset($reasonsMap[0])) {
            $reasonsMap[0] = 'حاضر';
        }

        // Query attendance records for these players on targetDate from SQL Server
        $playerIds = array_values(array_filter($players->pluck('player_id')->toArray()));
        $attendanceMap = [];

        if (!empty($playerIds)) {
            $conn = SqlServerApiRepository::startConnection();
            if ($conn) {
                $placeholders = implode(',', array_fill(0, count($playerIds), '?'));
                $sql = "SELECT RowID, PlayerRowID, ReasonKey, Comments, Visit, WhenInserted 
                        FROM FBall.dbo.tbl_Players_Attendance 
                        WHERE PlayerRowID IN ($placeholders) AND CAST(TheDate AS DATE) = ?";
                $params = array_merge($playerIds, [$targetDate]);
                $stmt = \sqlsrv_query($conn, $sql, $params);

                if ($stmt) {
                    while ($row = \sqlsrv_fetch_object($stmt)) {
                        $attendanceMap[$row->PlayerRowID] = [
                            'row_id'      => (int) $row->RowID,
                            'reason_key'  => (int) $row->ReasonKey,
                            'reason_text' => $reasonsMap[$row->ReasonKey] ?? 'غير محدد',
                            'comments'    => $row->Comments,
                            'visit'       => $row->Visit,
                        ];
                    }
                }
                \sqlsrv_close($conn);
            }
        }

        // Attach attendance info to each player model
        foreach ($players as $player) {
            $player->attendance_info = $attendanceMap[$player->player_id] ?? null;
        }

        $formattedPlayers = array_values(collect(OfficialTeamPlayerResource::collection($players))->sortBy('name')->toArray());

        return [
            'data'    => $formattedPlayers,
            'message' => 'success',
            'code'    => HttpCode::SUCCESS,
        ];
    }
}
