<?php
 
namespace App\Repositories\Api\V2;
 
use App\Entities\HttpCode;
use App\Entities\ImageType;
use App\Entities\Key;
use App\Entities\Status;
use App\Http\Resources\ActionDetailsResource;
use App\Http\Resources\ActionResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CommitteeResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\IntroResource;
use App\Http\Resources\NewDetailsResource;
use App\Http\Resources\NewResource;
use App\Http\Resources\SportGameResource;
use App\Http\Resources\TeamResource;
use App\Models\Action;
use App\Models\Category;
use App\Models\Committee;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Image;
use App\Models\Intro;
use App\Models\News;
use App\Models\Setting;
use App\Models\SportGame;
use App\Models\SportTeam;
use App\Models\HrEmployeeCategory;
use App\Models\HrEmployee;
use App\Models\HrAttendanceRecord;
use App\Models\HrLeaveType;
use App\Models\HrLeaveRequest;
use App\Models\HrDocument;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Models\User;
use App\Models\UserTeam;
use App\Repositories\General\UtilsRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
 
class SqlServerApiRepository
{
 
 
    public static function startConnection()
    {
        if (! function_exists('sqlsrv_connect')) {
            return false;
        }
 
        $serverName = 'dhsckarem.ddns.net';
        $uid = 'dhclubapp';
        $pwd = 'bNHW^3&q1mH5';
        $databaseName = 'MobileApp';
 
        $connectionInfo = [
            "UID" => $uid,
            "PWD" => $pwd,
            "Database" => $databaseName,
            "TrustServerCertificate" => true,
            "CharacterSet" => "UTF-8",
            "LoginTimeout" => 15,
        ];
        /* Connect using SQL Server Authentication. */
        $conn = \sqlsrv_connect($serverName, $connectionInfo);
        if ($conn) {
            return $conn;
        }
        return false;
    }
 
    /**
     * Test connectivity to the SQL Server used by this repository (same credentials as startConnection).
     *
     * @return array{ok: bool, message: string}
     */
    public static function testConnection(): array
    {
        if (! function_exists('sqlsrv_connect')) {
            return [
                'ok' => false,
                'message' => 'PHP extension sqlsrv is not loaded. Install ext-sqlsrv for your PHP version.',
            ];
        }
 
        $conn = self::startConnection();
        if ($conn) {
            if (function_exists('sqlsrv_close')) {
                sqlsrv_close($conn);
            }
 
            return [
                'ok' => true,
                'message' => 'Connected successfully to SQL Server (database MobileApp).',
            ];
        }
 
        $errors = function_exists('sqlsrv_errors') ? sqlsrv_errors() : [];
        $message = 'Connection failed.';
        if (! empty($errors)) {
            $message .= ' ' . json_encode($errors, JSON_UNESCAPED_UNICODE);
        }
 
        return [
            'ok' => false,
            'message' => $message,
        ];
    }
 
    public static function getSports()
    {
        $data = [];
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "SELECT RowID, NameAR, NameEN FROM dbo.MobileApp_Sports";
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    $data[] = [
                        'id' => $object->RowID,
                        'name_en' => $object->NameEN,
                        'name_ar' => $object->NameAR
                    ];
                }
            }
            sqlsrv_close($conn);
        }
        return response()->json($data);
    }
 
    public static function getTeams()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "SELECT SportID, TeamAR, TeamEN, TeamRowID, Email FROM dbo.MobileApp_Teams";
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    SportTeam::updateOrCreate([
                        'team_id' => $object->TeamRowID
                    ], [
                        'sport_id' => $object->SportID,
                        'name_en' => $object->TeamEN,
                        'name_ar' => $object->TeamAR,
                        'email' => $object->Email
                    ]);
 
                }
            }
            sqlsrv_close($conn);
        }
    }
 
    public static function getTeamPlayers()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "SELECT TeamRowID, PNameAR, PNameEN, PlayerRowID FROM dbo.MobileApp_Players ORDER BY PlayerRowID DESC";
            $result = \sqlsrv_query($conn, $sql);
            if ($result !== false) {
                $count = 0;
                while ($object = sqlsrv_fetch_object($result)) {
                    TeamPlayer::updateOrCreate([
                        'player_id' => $object->PlayerRowID,
                        'team_id' => $object->TeamRowID,
                    ], [
                        'player_id' => $object->PlayerRowID,
                        'team_id' => $object->TeamRowID,
                        'name_en' => $object->PNameEN,
                        'name_ar' => $object->PNameAR,
                    ]);
                }
            }
            sqlsrv_close($conn);
        }
    }
 
    public static function deleteTeamPlayers()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $players = TeamPlayer::get();
            foreach ($players as $player) {
                $playerId = $player->player_id;
                $sql = "SELECT TeamRowID, PNameAR, PNameEN, PlayerRowID FROM dbo.MobileApp_Players WHERE PlayerRowID=$playerId";
                if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                    $object = sqlsrv_fetch_object($result);
                    if (!$object) {
                        $player->forceDelete();
                    }
                }
            }
            sqlsrv_close($conn);
        }
    }
 
    public static function syncPlayersWithSqlServer(): array
    {
        $conn = SqlServerApiRepository::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT TeamRowID, PNameAR, PNameEN, PlayerRowID FROM dbo.MobileApp_Players";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            TeamPlayer::updateOrCreate(
                ['player_id' => $object->PlayerRowID, 'team_id' => $object->TeamRowID],
                ['name_en' => $object->PNameEN, 'name_ar' => $object->PNameAR]
            );
            $sqlServerIds[] = $object->PlayerRowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = TeamPlayer::whereNotIn('player_id', $sqlServerIds)->count();
            TeamPlayer::whereNotIn('player_id', $sqlServerIds)->delete();
        }
 
        return $stats;
    }
 
    public static function syncTeamsWithSqlServer(): array
    {
        $conn = SqlServerApiRepository::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT SportID, TeamAR, TeamEN, TeamRowID, Email FROM dbo.MobileApp_Teams";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            SportTeam::updateOrCreate(
                ['team_id' => $object->TeamRowID],
                ['sport_id' => $object->SportID, 'name_en' => $object->TeamEN, 'name_ar' => $object->TeamAR, 'email' => $object->Email]
            );
            $sqlServerIds[] = $object->TeamRowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = SportTeam::whereNotIn('team_id', $sqlServerIds)->count();
            SportTeam::whereNotIn('team_id', $sqlServerIds)->delete();
        }
 
        return $stats;
    }
 
    public static function syncUsersWithSqlServer(): array
    {
        $conn = SqlServerApiRepository::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT UserID, UserEN, UserAR, Username, Password, Role FROM dbo.MobileApp_Users ORDER BY UserID DESC";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            User::updateOrCreate(
                ['email' => $object->Username . '@dhclubapp.xyz'],
                [
                    'user_id'  => $object->UserID,
                    'name'     => $object->UserEN,
                    'password' => Hash::make($object->Password),
                    'role'     => $object->Role,
                    'status'   => Status::ACTIVE,
                    'lang'     => 'en',
                ]
            );
            $sqlServerIds[] = $object->UserID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        // Only delete users that originated from SQL Server (user_id IS NOT NULL)
        // to avoid removing locally created admins/fans
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = User::whereNotNull('user_id')
                ->whereNotIn('user_id', $sqlServerIds)
                ->count();
            User::whereNotNull('user_id')
                ->whereNotIn('user_id', $sqlServerIds)
                ->delete();
        }
 
        return $stats;
    }
 
    public static function syncUserTeamsWithSqlServer(): array
    {
        $conn = SqlServerApiRepository::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT UserID, TeamsRowID, FullTeamNames, OfficialID FROM dbo.V_Official_Teams";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerOfficialIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            $user      = User::where('user_id', $object->UserID)->first();
            $sportTeam = SportTeam::where('team_id', $object->TeamsRowID)->first();
 
            if ($user && $sportTeam) {
                UserTeam::updateOrCreate(
                    ['official_id' => $object->OfficialID],
                    [
                        'user_id'        => $user->id,
                        'team_id'        => $sportTeam->id,
                        'full_team_name' => $object->FullTeamNames,
                    ]
                );
                $stats['upserted']++;
            }
 
            $sqlServerOfficialIds[] = $object->OfficialID;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerOfficialIds)) {
            $stats['deleted'] = UserTeam::whereNotIn('official_id', $sqlServerOfficialIds)->count();
            UserTeam::whereNotIn('official_id', $sqlServerOfficialIds)->delete();
        }
 
        return $stats;
    }
 
    public static function getPlayerImage($conn, $playerId)
    {
        $sql = "SELECT TOP 1 PlayerPhoto,PlayerRowID FROM dbo.MobileApp_PlayersPhotos WHERE PlayerRowID=$playerId AND PlayerPhoto IS NOT NULL";
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_path = 'uploads/players/';
        $result = \sqlsrv_query($conn, $sql);
        if ($result !== false) {
            $object = sqlsrv_fetch_object($result);
            if ($object) {
                $base64 = base64_encode($object->PlayerPhoto);
                return UtilsRepository::createImageBase64($base64, $image_path, $file_id, 282, 561);
            }
        }
        return null;
    }
 
    public static function getPlayerImages()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $players = TeamPlayer::where('image', '=', null)
                ->inRandomOrder()->limit(50)->get();
            foreach ($players as $player) {
                TeamPlayer::where(['player_id' => $player->player_id])->update([
                    'image' => self::getPlayerImage($conn, $player->player_id)
                ]);
            }
        }
    }
 
    public static function syncPlayerImages(callable $onProgress = null): array
    {
        $conn = SqlServerApiRepository::startConnection();
        $stats = ['processed' => 0, 'updated' => 0, 'skipped' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        TeamPlayer::where('image', '=', null)
            ->orderBy('id')
            ->chunk(100, function ($players) use ($conn, &$stats, $onProgress) {
                foreach ($players as $player) {
                    $image = self::getPlayerImage($conn, $player->player_id);
                    $stats['processed']++;
 
                    if ($image) {
                        TeamPlayer::where(['player_id' => $player->player_id])->update(['image' => $image]);
                        $stats['updated']++;
                    } else {
                        $stats['skipped']++;
                    }
 
                    if ($onProgress) {
                        $onProgress($stats);
                    }
                }
            });
 
        sqlsrv_close($conn);
 
        return $stats;
    }
 
    public static function getTeamImages()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $teams = SportTeam::where('image', '=', null)->limit(1)->get();
            foreach ($teams as $team) {
                $team->update([
                    'image' => self::getTeamImage($conn, $team->team_id)
                ]);
            }
        }
    }
 
    public static function getTeamImage($conn, $teamId)
    {
        $sql = "SELECT TOP 1 Photo FROM dbo.MobileApp_TeamsPhotos WHERE TeamsRowID=$teamId";
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_path = 'uploads/sport_teams/';
        if (($result = \sqlsrv_query($conn, $sql)) !== false) {
            $object = sqlsrv_fetch_object($result);
            if ($object) {
                return UtilsRepository::createImageBase64(base64_encode($object->Photo), $image_path, $file_id, 282, 561);
            }
        }
        return null;
    }
 
    public static function getUsers()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "SELECT UserID , UserEN , UserAR , Username , Password , Role FROM dbo.MobileApp_Users ORDER BY UserID DESC";
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    if (User::where(['email' => $object->Username . '@dhclubapp.xyz',])->first()) {
                        continue;
                    }
                    User::updateOrCreate([
                        'user_id' => $object->UserID,
                    ], [
                        'user_id' => $object->UserID,
                        'name' => $object->UserEN,
                        'email' => $object->Username . '@dhclubapp.xyz',
                        'password' => Hash::make($object->Password),
                        'role' => $object->Role,
                        'status' => Status::ACTIVE,
                        'lang' => 'en'
                    ]);
                }
            }
            sqlsrv_close($conn);
        }
    }
 
    public static function getUserTeams()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "SELECT UserID , TeamsRowID , FullTeamNames, OfficialID FROM dbo.V_Official_Teams";//MobileApp_Officials_Teams
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    $user = User::where(['user_id' => $object->UserID,])->first();
                    $sportTeam = SportTeam::where(['team_id' => $object->TeamsRowID,])->first();
                    if ($user && $sportTeam) {
                        UserTeam::updateOrCreate([
                            'official_id' => $object->OfficialID,
                        ], [
                            'official_id' => $object->OfficialID,
                            'user_id' => $user->id,
                            'team_id' => $sportTeam->id,
                            'full_team_name' => $object->FullTeamNames,
                        ]);
                    }
                }
            }
            sqlsrv_close($conn);
        }
    }
 
    public static function getSeasonTeamPlayerId($conn, $player_id)
    {
        $player = TeamPlayer::where(['player_id' => $player_id])
            ->orderBy('id', 'desc')
            ->first();
        $teamId = $player->team_id;
        $sql = "SELECT TOP 1 SeasonTeamPlayerRowID FROM dbo.QSeasonTeamPlayer WHERE PlayerRowID=" . $player_id . " AND TeamRowID=" . $teamId;
        if (($result = \sqlsrv_query($conn, $sql)) !== false) {
            $object = sqlsrv_fetch_object($result);
            if ($object) {
                return $object->SeasonTeamPlayerRowID;
            }
        }
        return 0;
    }
 
    /**
     * Sync player details (birth date, nationality, height, weight, shirt number, position)
     * from the MobileApp database View: dbo.MobileApp_Players
     */
    public static function syncPlayerDetailsFromMobileApp(): array
    {
        $conn = self::startConnection();
        $stats = ['updated' => 0, 'skipped' => 0];
 
        if (!$conn) {
            Log::warning('syncPlayerDetailsFromMobileApp: Could not connect to SQL Server');
            return $stats;
        }
 
        $sql = "SELECT PlayerRowID, DOB, Nationality, Length, Weight, Position, JerseyNo FROM dbo.MobileApp_Players";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            Log::warning('syncPlayerDetailsFromMobileApp: Query failed');
            sqlsrv_close($conn);
            return $stats;
        }
 
        while ($object = \sqlsrv_fetch_object($result)) {
            $player = TeamPlayer::where('player_id', $object->PlayerRowID)->first();
            if (!$player) {
                $stats['skipped']++;
                continue;
            }
 
            $birthDate = null;
            if ($object->DOB instanceof \DateTime) {
                $birthDate = $object->DOB->format('Y-m-d');
            } elseif (is_string($object->DOB) && !empty($object->DOB)) {
                $birthDate = $object->DOB;
            }
 
            $player->update([
                'birth_date'     => $birthDate,
                'nationality_ar' => $object->Nationality ?? null,
                'nationality_en' => $object->Nationality ?? null,
                'height'         => $object->Length ? (int)$object->Length : null,
                'weight'         => $object->Weight ? (int)$object->Weight : null,
                'number'         => $object->JerseyNo ?? $player->number,
                'position_ar'    => $object->Position ?? null,
                'position_en'    => $object->Position ?? null,
            ]);
            $stats['updated']++;
        }
 
        sqlsrv_close($conn);
 
        return $stats;
    }
 
    public static function syncMatchesWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT RowID, SeasonRowID, CompetitionRowID, Team1, Team2, MatchDate, MatchTime, StageRound, MatchNumber, Week, Pitch, Remarks, Team1Result, Team2Result, MatchInHouse, FANETMatchID, LiveLink, Team1RowID, Team2RowID FROM FBall.dbo.tblMatches";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            $matchDate = null;
            if ($object->MatchDate instanceof \DateTime) {
                $matchDate = $object->MatchDate->format('Y-m-d');
            }
            
            $matchTime = null;
            if ($object->MatchTime instanceof \DateTime) {
                $matchTime = $object->MatchTime->format('H:i:s');
            } elseif (is_string($object->MatchTime)) {
                $matchTime = $object->MatchTime;
            }
 
            \App\Models\SportMatch::updateOrCreate(
                ['row_id' => $object->RowID],
                [
                    'season_row_id'      => $object->SeasonRowID,
                    'competition_row_id' => $object->CompetitionRowID,
                    'team1'              => $object->Team1,
                    'team1_row_id'       => $object->Team1RowID,
                    'team2'              => $object->Team2,
                    'team2_row_id'       => $object->Team2RowID,
                    'match_date'         => $matchDate,
                    'match_time'         => $matchTime,
                    'stage_round'        => $object->StageRound,
                    'match_number'       => $object->MatchNumber,
                    'week'               => $object->Week,
                    'pitch'              => $object->Pitch,
                    'remarks'            => $object->Remarks,
                    'team1_result'       => $object->Team1Result,
                    'team2_result'       => $object->Team2Result,
                    'match_in_house'     => $object->MatchInHouse,
                    'fanet_match_id'     => $object->FANETMatchID,
                    'live_link'          => $object->LiveLink,
                ]
            );
            $sqlServerIds[] = $object->RowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = \App\Models\SportMatch::whereNotIn('row_id', $sqlServerIds)->count();
            \App\Models\SportMatch::whereNotIn('row_id', $sqlServerIds)->delete();
        }
 
        return $stats;
    }
 
    public static function syncSeasonsWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT RowID, SName, Sstart, Send, SNotes, Active FROM FBall.dbo.tblSeasons";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            $startDate = null;
            if ($object->Sstart instanceof \DateTime) {
                $startDate = $object->Sstart->format('Y-m-d');
            }
            
            $endDate = null;
            if ($object->Send instanceof \DateTime) {
                $endDate = $object->Send->format('Y-m-d');
            }
 
            \App\Models\Season::updateOrCreate(
                ['row_id' => $object->RowID],
                [
                    'name'       => $object->SName,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'notes'      => $object->SNotes,
                    'active'     => $object->Active,
                ]
            );
            $sqlServerIds[] = $object->RowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = \App\Models\Season::whereNotIn('row_id', $sqlServerIds)->count();
            \App\Models\Season::whereNotIn('row_id', $sqlServerIds)->delete();
        }
 
        return $stats;
    }
 
    public static function syncAttendReasonsWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT RowID, TheReason, ReasonKey, TheOrder, GlobalReason FROM FBall.dbo.tbl_Attend_Reasons";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            \App\Models\AttendReason::updateOrCreate(
                ['row_id' => $object->RowID],
                [
                    'reason'        => $object->TheReason,
                    'reason_key'    => $object->ReasonKey,
                    'the_order'     => $object->TheOrder,
                    'global_reason' => $object->GlobalReason,
                ]
            );
            $sqlServerIds[] = $object->RowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = \App\Models\AttendReason::whereNotIn('row_id', $sqlServerIds)->count();
            \App\Models\AttendReason::whereNotIn('row_id', $sqlServerIds)->delete();
        }
 
        return $stats;
    }
 
    public static function syncClubsWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT RowID, NameAR, NameEN, Logo FROM dbo.MobileApp_Clubs";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            $logoPath = null;
            if (!empty($object->Logo)) {
                $base64 = base64_encode($object->Logo);
                $logoPath = \App\Repositories\General\UtilsRepository::createImageBase64(
                    $base64,
                    'uploads/clubs/',
                    'club_' . $object->RowID
                );
            }
 
            \App\Models\Club::updateOrCreate(
                ['row_id' => $object->RowID],
                [
                    'name_ar' => $object->NameAR,
                    'name_en' => $object->NameEN,
                    'logo'    => $logoPath,
                ]
            );
            $sqlServerIds[] = $object->RowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = \App\Models\Club::whereNotIn('row_id', $sqlServerIds)->count();
            
            $deletedClubs = \App\Models\Club::whereNotIn('row_id', $sqlServerIds)->get();
            foreach ($deletedClubs as $club) {
                if ($club->logo && file_exists(public_path($club->logo))) {
                    @unlink(public_path($club->logo));
                }
                $club->delete();
            }
        }
 
        return $stats;
    }

    public static function syncCompetitionsWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT c.RowID, c.SeasonRowID, c.CName, c.CNameEN, c.WeeksNo, c.SportID, c.TheOrder, c.MobileAppHeaderComp, l.CompetitionLogo 
                FROM dbo.MobileApp_Competitions c
                LEFT JOIN dbo.MobileApp_Competitions_Logos l ON c.RowID = l.CompetitionRowID";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            $logoPath = null;
            if (!empty($object->CompetitionLogo)) {
                $base64 = base64_encode($object->CompetitionLogo);
                $logoPath = \App\Repositories\General\UtilsRepository::createImageBase64(
                    $base64,
                    'uploads/competitions/',
                    'comp_' . $object->RowID
                );
            }
 
            \App\Models\Competition::updateOrCreate(
                ['row_id' => $object->RowID],
                [
                    'season_row_id' => $object->SeasonRowID,
                    'name_ar'       => $object->CName,
                    'name_en'       => $object->CNameEN,
                    'sport_id'      => $object->SportID,
                    'weeks_no'      => $object->WeeksNo,
                    'the_order'     => $object->TheOrder,
                    'logo'          => $logoPath,
                    'mobile_app_header_comp' => $object->MobileAppHeaderComp ? 1 : 0,
                ]
            );
            $sqlServerIds[] = $object->RowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = \App\Models\Competition::whereNotIn('row_id', $sqlServerIds)->count();
            
            $deleted = \App\Models\Competition::whereNotIn('row_id', $sqlServerIds)->get();
            foreach ($deleted as $comp) {
                if ($comp->logo && file_exists(public_path($comp->logo))) {
                    @unlink(public_path($comp->logo));
                }
                $comp->delete();
            }
        }
 
        return $stats;
    }

    public static function syncLeagueStandingsWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT CompetitionRowID, ClubID, TheTeam, Play, Win, Draw, Lose, Has, Against, [Diff.], Points, [Order] FROM dbo.MobileApp_CompetitionsStandings";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        // We will just clear and re-insert since standings change often
        \App\Models\LeagueStanding::truncate();

        while ($object = \sqlsrv_fetch_object($result)) {
            \App\Models\LeagueStanding::create([
                'competition_row_id' => $object->CompetitionRowID,
                'club_id'            => $object->ClubID,
                'team_name'          => $object->TheTeam,
                'play'               => $object->Play,
                'win'                => $object->Win,
                'draw'               => $object->Draw,
                'lose'               => $object->Lose,
                'goals_for'          => $object->Has,
                'goals_against'      => $object->Against,
                'goals_diff'         => $object->{'Diff.'},
                'points'             => $object->Points,
                'rank'               => $object->Order,
            ]);
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        return $stats;
    }
 
    /**
     * Sync clinic time slots from SQL Server (MobileApp_Clinic_TimeSlots view).
     */
    public static function syncClinicTimeSlotsWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0, 'deleted' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        $sql = "SELECT RowID, DayOfWeek, StartTime, EndTime, MaxBookings, Active FROM dbo.MobileApp_Clinic_TimeSlots";
        $result = \sqlsrv_query($conn, $sql);
 
        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }
 
        $sqlServerIds = [];
        while ($object = \sqlsrv_fetch_object($result)) {
            // StartTime/EndTime come as datetime from SQL Server, extract time portion
            $startTime = null;
            if ($object->StartTime instanceof \DateTime) {
                $startTime = $object->StartTime->format('H:i');
            } elseif (is_string($object->StartTime)) {
                $startTime = substr($object->StartTime, 0, 5);
            }

            $endTime = null;
            if ($object->EndTime instanceof \DateTime) {
                $endTime = $object->EndTime->format('H:i');
            } elseif (is_string($object->EndTime)) {
                $endTime = substr($object->EndTime, 0, 5);
            }

            \App\Models\ClinicTimeSlot::updateOrCreate(
                ['row_id' => $object->RowID],
                [
                    'day_of_week'  => $object->DayOfWeek,
                    'start_time'   => $startTime,
                    'end_time'     => $endTime,
                    'max_bookings' => $object->MaxBookings ?? 1,
                    'status'       => $object->Active ?? 1,
                ]
            );
            $sqlServerIds[] = $object->RowID;
            $stats['upserted']++;
        }
 
        sqlsrv_close($conn);
 
        if (!empty($sqlServerIds)) {
            $stats['deleted'] = \App\Models\ClinicTimeSlot::whereNotNull('row_id')
                ->whereNotIn('row_id', $sqlServerIds)->count();
            \App\Models\ClinicTimeSlot::whereNotNull('row_id')
                ->whereNotIn('row_id', $sqlServerIds)->delete();
        }
 
        return $stats;
    }

    /**
     * Push un-synced clinic bookings TO SQL Server (MobileApp_Clinic_Bookings view/table).
     * This is a REVERSE sync: we INSERT/UPDATE our bookings into Karim's database.
     */
    public static function pushClinicBookingsToSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['pushed' => 0, 'failed' => 0];
 
        if (!$conn) {
            return $stats;
        }
 
        // Get all bookings that haven't been synced yet, or were updated after last sync
        $bookings = \App\Models\ClinicBooking::where('synced_to_sqlserver', false)
            ->with(['timeSlot'])
            ->get();
 
        foreach ($bookings as $booking) {
            $timeSlotRowId = $booking->timeSlot ? $booking->timeSlot->row_id : null;

            // Skip if time slot has no row_id (not from SQL Server)
            if (!$timeSlotRowId) {
                continue;
            }

            $patientName = $booking->patient_name;
            $patientPhone = $booking->patient_phone;

            // Check if this booking already exists in SQL Server
            $checkSql = "SELECT RowID FROM dbo.MobileApp_Clinic_Bookings WHERE AppBookingID = ?";
            $checkResult = \sqlsrv_query($conn, $checkSql, [$booking->id]);
            
            if ($checkResult && \sqlsrv_fetch($checkResult)) {
                // UPDATE existing
                $updateSql = "UPDATE dbo.MobileApp_Clinic_Bookings SET 
                    PatientName = ?, PatientPhone = ?, TimeSlotRowID = ?, 
                    BookingDate = ?, IsForOther = ?, OtherName = ?, OtherPhone = ?,
                    Description = ?, Status = ?, UpdatedAt = ?
                    WHERE AppBookingID = ?";

                $params = [
                    $patientName,
                    $patientPhone,
                    $timeSlotRowId,
                    $booking->booking_date,
                    $booking->is_for_other,
                    $booking->other_name,
                    $booking->other_phone,
                    $booking->description,
                    $booking->status,
                    $booking->updated_at ? $booking->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    $booking->id,
                ];

                $updateResult = \sqlsrv_query($conn, $updateSql, $params);
                if ($updateResult !== false) {
                    $booking->update(['synced_to_sqlserver' => true]);
                    $stats['pushed']++;
                } else {
                    $stats['failed']++;
                    Log::warning('Clinic booking UPDATE failed for ID: ' . $booking->id, [
                        'errors' => \sqlsrv_errors()
                    ]);
                }
            } else {
                // INSERT new
                $insertSql = "INSERT INTO dbo.MobileApp_Clinic_Bookings 
                    (AppBookingID, PatientName, PatientPhone, TimeSlotRowID, BookingDate, 
                     IsForOther, OtherName, OtherPhone, Description, Status, CreatedAt, UpdatedAt) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $params = [
                    $booking->id,
                    $patientName,
                    $patientPhone,
                    $timeSlotRowId,
                    $booking->booking_date,
                    $booking->is_for_other,
                    $booking->other_name,
                    $booking->other_phone,
                    $booking->description,
                    $booking->status,
                    $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    $booking->updated_at ? $booking->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                ];

                $insertResult = \sqlsrv_query($conn, $insertSql, $params);
                if ($insertResult !== false) {
                    $booking->update(['synced_to_sqlserver' => true]);
                    $stats['pushed']++;
                } else {
                    $stats['failed']++;
                    Log::warning('Clinic booking INSERT failed for ID: ' . $booking->id, [
                        'errors' => \sqlsrv_errors()
                    ]);
                }
            }
        }
 
        sqlsrv_close($conn);
 
        return $stats;
    }
    /**
     * Push a SINGLE clinic booking to SQL Server in real-time.
     * Called immediately after a new booking is created.
     * If this fails, the cron-based pushClinicBookingsToSqlServer() will retry.
     */
    public static function pushSingleBookingToSqlServer(\App\Models\ClinicBooking $booking): bool
    {
        $conn = self::startConnection();
        if (!$conn) {
            return false;
        }

        $booking->load('timeSlot');
        $timeSlotRowId = $booking->timeSlot ? $booking->timeSlot->row_id : null;

        if (!$timeSlotRowId) {
            sqlsrv_close($conn);
            return false;
        }

        $patientName = $booking->patient_name;
        $patientPhone = $booking->patient_phone;

        // Check if already exists
        $checkSql = "SELECT RowID FROM dbo.MobileApp_Clinic_Bookings WHERE AppBookingID = ?";
        $checkResult = \sqlsrv_query($conn, $checkSql, [$booking->id]);

        if ($checkResult && \sqlsrv_fetch($checkResult)) {
            // UPDATE existing
            $updateSql = "UPDATE dbo.MobileApp_Clinic_Bookings SET 
                PatientName = ?, PatientPhone = ?, TimeSlotRowID = ?, 
                BookingDate = ?, IsForOther = ?, OtherName = ?, OtherPhone = ?,
                Description = ?, Status = ?, UpdatedAt = ?
                WHERE AppBookingID = ?";

            $params = [
                $patientName,
                $patientPhone,
                $timeSlotRowId,
                $booking->booking_date,
                $booking->is_for_other,
                $booking->other_name,
                $booking->other_phone,
                $booking->description,
                $booking->status,
                now()->format('Y-m-d H:i:s'),
                $booking->id,
            ];

            $result = \sqlsrv_query($conn, $updateSql, $params);
        } else {
            // INSERT new
            $insertSql = "INSERT INTO dbo.MobileApp_Clinic_Bookings 
                (AppBookingID, PatientName, PatientPhone, TimeSlotRowID, BookingDate, 
                 IsForOther, OtherName, OtherPhone, Description, Status, CreatedAt, UpdatedAt) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $booking->id,
                $patientName,
                $patientPhone,
                $timeSlotRowId,
                $booking->booking_date,
                $booking->is_for_other,
                $booking->other_name,
                $booking->other_phone,
                $booking->description,
                $booking->status,
                $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                $booking->updated_at ? $booking->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            ];

            $result = \sqlsrv_query($conn, $insertSql, $params);
        }

        sqlsrv_close($conn);

        if ($result !== false) {
            $booking->update(['synced_to_sqlserver' => true]);
            return true;
        }

        Log::warning('Real-time pushSingleBooking failed for ID: ' . $booking->id, [
            'errors' => \sqlsrv_errors()
        ]);
        return false;
    }

    // ==========================================
    // HR MODULE SYNC METHODS (PULL & PUSH)
    // ==========================================

    /**
     * 1. Pull HR Employee Categories from SQL Server
     */
    public static function syncHrEmployeeCategoriesWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0];

        if (!$conn) {
            return $stats;
        }

        $sql = "SELECT CategoryID, CategoryNameAR, CategoryNameEN, Active FROM dbo.MobileApp_HR_EmployeeCategories";
        $result = \sqlsrv_query($conn, $sql);

        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }

        while ($object = \sqlsrv_fetch_object($result)) {
            HrEmployeeCategory::updateOrCreate(
                ['row_id' => $object->CategoryID],
                [
                    'name_ar' => $object->CategoryNameAR ?? '',
                    'name_en' => $object->CategoryNameEN ?? null,
                    'active'  => isset($object->Active) ? (bool)$object->Active : true,
                ]
            );
            $stats['upserted']++;
        }

        sqlsrv_close($conn);
        return $stats;
    }

    /**
     * 2. Pull HR Employees from SQL Server
     * Also creates/updates local User accounts with role = 'employee'
     */
    public static function syncHrEmployeesWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0];

        if (!$conn) {
            return $stats;
        }

        $sql = "SELECT EmployeeRowID, CategoryID, NameAR, NameEN, JobTitle, Username, Password, Email FROM dbo.MobileApp_HR_Employees";
        $result = \sqlsrv_query($conn, $sql);

        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }

        while ($object = \sqlsrv_fetch_object($result)) {
            $baseUsername = !empty(trim($object->Username ?? '')) ? trim($object->Username) : ('emp_' . $object->EmployeeRowID);
            $username = $baseUsername;

            // Ensure username uniqueness
            $existingEmp = HrEmployee::where('username', $username)->where('row_id', '!=', $object->EmployeeRowID)->first();
            if ($existingEmp) {
                $username = $username . '_' . $object->EmployeeRowID;
            }

            $password = !empty(trim($object->Password ?? '')) ? trim($object->Password) : '123456';

            // Find category
            $category = null;
            if (!empty($object->CategoryID)) {
                $category = HrEmployeeCategory::where('row_id', $object->CategoryID)->first();
            }

            // Ensure email uniqueness
            $email = !empty(trim($object->Email ?? '')) ? trim($object->Email) : ($username . '@dhclubapp.xyz');
            $existingEmail = User::where('email', $email)->where(function($q) use ($object) {
                $q->where('role', '!=', 'employee')->orWhere('user_id', '!=', $object->EmployeeRowID);
            })->first();
            if ($existingEmail) {
                $email = $username . '_' . $object->EmployeeRowID . '@dhclubapp.xyz';
            }

            // Update/Create local User
            $user = User::where('role', 'employee')->where('user_id', $object->EmployeeRowID)->first();
            if ($user) {
                $user->update([
                    'email'    => $email,
                    'name'     => $object->NameAR ?: ($object->NameEN ?: $username),
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                ]);
            } else {
                $user = User::create([
                    'email'    => $email,
                    'user_id'  => $object->EmployeeRowID,
                    'name'     => $object->NameAR ?: ($object->NameEN ?: $username),
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'role'     => 'employee',
                    'status'   => Status::ACTIVE,
                    'lang'     => 'ar',
                ]);
            }

            // Handle Photo if present
            $photoPath = null;
            if (isset($object->Photo) && !empty($object->Photo)) {
                if (is_string($object->Photo) && (str_starts_with($object->Photo, 'http') || str_starts_with($object->Photo, '/uploads'))) {
                    $photoPath = $object->Photo;
                } else {
                    // Binary or raw photo
                    $dir = public_path('uploads/hr_employees');
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0755, true);
                    }
                    $fileName = 'employee_' . $object->EmployeeRowID . '.png';
                    file_put_contents($dir . '/' . $fileName, $object->Photo);
                    $photoPath = 'uploads/hr_employees/' . $fileName;
                }
            }

            HrEmployee::updateOrCreate(
                ['row_id' => $object->EmployeeRowID],
                [
                    'user_id'       => $user->id,
                    'category_id'   => $category ? $category->id : null,
                    'name_ar'       => $object->NameAR ?? '',
                    'name_en'       => $object->NameEN ?? null,
                    'job_title'     => $object->JobTitle ?? null,
                    'photo'         => $photoPath,
                    'username'      => $username,
                    'password_hash' => \Illuminate\Support\Facades\Hash::make($password),
                ]
            );

            $stats['upserted']++;
        }

        sqlsrv_close($conn);
        return $stats;
    }

    /**
     * 3. Pull HR Attendance Records from SQL Server
     */
    public static function syncHrAttendanceRecordsWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0];

        if (!$conn) {
            return $stats;
        }

        $sql = "SELECT RowID, EmployeeRowID, AttendanceDate, CheckInTime, CheckOutTime, Status, Notes FROM dbo.MobileApp_HR_AttendanceRecords";
        $result = \sqlsrv_query($conn, $sql);

        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }

        while ($object = \sqlsrv_fetch_object($result)) {
            $checkIn = null;
            if ($object->CheckInTime) {
                $checkIn = $object->CheckInTime instanceof \DateTime ? $object->CheckInTime->format('H:i:s') : (string)$object->CheckInTime;
            }
            $checkOut = null;
            if ($object->CheckOutTime) {
                $checkOut = $object->CheckOutTime instanceof \DateTime ? $object->CheckOutTime->format('H:i:s') : (string)$object->CheckOutTime;
            }
            $attDate = $object->AttendanceDate instanceof \DateTime ? $object->AttendanceDate->format('Y-m-d') : (string)$object->AttendanceDate;

            HrAttendanceRecord::updateOrCreate(
                ['row_id' => $object->RowID],
                [
                    'employee_row_id' => $object->EmployeeRowID,
                    'attendance_date' => $attDate,
                    'check_in_time'   => $checkIn,
                    'check_out_time'  => $checkOut,
                    'status'          => (int)($object->Status ?? 1),
                    'notes'           => $object->Notes ?? null,
                ]
            );
            $stats['upserted']++;
        }

        sqlsrv_close($conn);
        return $stats;
    }

    /**
     * 4. Pull HR Leave Types from SQL Server
     */
    public static function syncHrLeaveTypesWithSqlServer(): array
    {
        $conn = self::startConnection();
        $stats = ['upserted' => 0];

        if (!$conn) {
            return $stats;
        }

        $sql = "SELECT TypeID, TypeNameAR, TypeNameEN, Active FROM dbo.MobileApp_HR_LeaveTypes";
        $result = \sqlsrv_query($conn, $sql);

        if ($result === false) {
            sqlsrv_close($conn);
            return $stats;
        }

        while ($object = \sqlsrv_fetch_object($result)) {
            HrLeaveType::updateOrCreate(
                ['row_id' => $object->TypeID],
                [
                    'name_ar' => $object->TypeNameAR ?? '',
                    'name_en' => $object->TypeNameEN ?? null,
                    'active'  => isset($object->Active) ? (bool)$object->Active : true,
                ]
            );
            $stats['upserted']++;
        }

        sqlsrv_close($conn);
        return $stats;
    }

    /**
     * 5. Push HR Leave Requests to SQL Server (Batch / Cron)
     */
    public static function pushHrLeaveRequestsToSqlServer(): array
    {
        $stats = ['pushed' => 0, 'failed' => 0];
        $pendingRequests = HrLeaveRequest::where('synced_to_sqlserver', false)->get();

        foreach ($pendingRequests as $req) {
            if (self::pushSingleHrLeaveRequestToSqlServer($req)) {
                $stats['pushed']++;
            } else {
                $stats['failed']++;
            }
        }

        return $stats;
    }

    /**
     * Push a SINGLE HR Leave Request to SQL Server (Real-time + Binary attachment support)
     */
    public static function pushSingleHrLeaveRequestToSqlServer(HrLeaveRequest $leaveRequest): bool
    {
        $conn = self::startConnection();
        if (!$conn) {
            return false;
        }

        // Read attachment as Binary
        $attachmentData = null;
        if (!empty($leaveRequest->attachment_path)) {
            $fullPath = public_path($leaveRequest->attachment_path);
            if (!file_exists($fullPath)) {
                $fullPath = storage_path('app/public/' . ltrim($leaveRequest->attachment_path, '/'));
            }
            if (file_exists($fullPath)) {
                $attachmentData = file_get_contents($fullPath);
            }
        }

        // Check if exists in SQL Server by RequestID
        $checkSql = "SELECT RequestID FROM dbo.MobileApp_HR_LeaveRequests WHERE RequestID = ?";
        $checkResult = \sqlsrv_query($conn, $checkSql, [$leaveRequest->id]);

        $leaveTypeRowId = $leaveRequest->leaveType ? $leaveRequest->leaveType->row_id : $leaveRequest->leave_type_id;

        if ($checkResult && \sqlsrv_fetch($checkResult)) {
            $sql = "UPDATE dbo.MobileApp_HR_LeaveRequests SET 
                    EmployeeRowID = ?, TypeID = ?, StartDate = ?, EndDate = ?, Description = ?, 
                    AttachmentUrl = ?, Status = ?, AdminReplyNotes = ?
                    WHERE RequestID = ?";

            $params = [
                $leaveRequest->employee_row_id,
                $leaveTypeRowId,
                $leaveRequest->start_date,
                $leaveRequest->end_date,
                $leaveRequest->description,
                $attachmentData !== null ? [ $attachmentData, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max') ] : null,
                $leaveRequest->status,
                $leaveRequest->admin_reply_notes,
                $leaveRequest->id,
            ];
            $result = \sqlsrv_query($conn, $sql, $params);
        } else {
            $sql = "INSERT INTO dbo.MobileApp_HR_LeaveRequests 
                    (RequestID, EmployeeRowID, TypeID, StartDate, EndDate, Description, AttachmentUrl, Status, CreatedAt, AdminReplyNotes)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $leaveRequest->id,
                $leaveRequest->employee_row_id,
                $leaveTypeRowId,
                $leaveRequest->start_date,
                $leaveRequest->end_date,
                $leaveRequest->description,
                $attachmentData !== null ? [ $attachmentData, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max') ] : null,
                $leaveRequest->status,
                $leaveRequest->created_at ? $leaveRequest->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                $leaveRequest->admin_reply_notes,
            ];
            $result = \sqlsrv_query($conn, $sql, $params);
        }

        sqlsrv_close($conn);

        if ($result !== false) {
            $leaveRequest->update(['synced_to_sqlserver' => true]);
            return true;
        }

        Log::warning('Push HR LeaveRequest to SQL Server failed for ID: ' . $leaveRequest->id, [
            'errors' => \sqlsrv_errors()
        ]);
        return false;
    }

    /**
     * 6. Push HR Documents to SQL Server (Batch / Cron)
     */
    public static function pushHrDocumentsToSqlServer(): array
    {
        $stats = ['pushed' => 0, 'failed' => 0];
        $pendingDocs = HrDocument::where('synced_to_sqlserver', false)->get();

        foreach ($pendingDocs as $doc) {
            if (self::pushSingleHrDocumentToSqlServer($doc)) {
                $stats['pushed']++;
            } else {
                $stats['failed']++;
            }
        }

        return $stats;
    }

    /**
     * Push a SINGLE HR Document to SQL Server (Real-time + Binary attachment support)
     */
    public static function pushSingleHrDocumentToSqlServer(HrDocument $doc): bool
    {
        $conn = self::startConnection();
        if (!$conn) {
            return false;
        }

        // Read attachment as Binary
        $attachmentData = null;
        if (!empty($doc->attachment_path)) {
            $fullPath = public_path($doc->attachment_path);
            if (!file_exists($fullPath)) {
                $fullPath = storage_path('app/public/' . ltrim($doc->attachment_path, '/'));
            }
            if (file_exists($fullPath)) {
                $attachmentData = file_get_contents($fullPath);
            }
        }

        // Check if exists by DocumentID
        $checkSql = "SELECT DocumentID FROM dbo.MobileApp_HR_Documents WHERE DocumentID = ?";
        $checkResult = \sqlsrv_query($conn, $checkSql, [$doc->id]);

        if ($checkResult && \sqlsrv_fetch($checkResult)) {
            $sql = "UPDATE dbo.MobileApp_HR_Documents SET 
                    EmployeeRowID = ?, Description = ?, AttachmentUrl = ?
                    WHERE DocumentID = ?";

            $params = [
                $doc->employee_row_id,
                $doc->description,
                $attachmentData !== null ? [ $attachmentData, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max') ] : null,
                $doc->id,
            ];
            $result = \sqlsrv_query($conn, $sql, $params);
        } else {
            $sql = "INSERT INTO dbo.MobileApp_HR_Documents 
                    (DocumentID, EmployeeRowID, Description, AttachmentUrl, CreatedAt)
                    VALUES (?, ?, ?, ?, ?)";

            $params = [
                $doc->id,
                $doc->employee_row_id,
                $doc->description,
                $attachmentData !== null ? [ $attachmentData, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max') ] : null,
                $doc->created_at ? $doc->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            ];
            $result = \sqlsrv_query($conn, $sql, $params);
        }

        sqlsrv_close($conn);

        if ($result !== false) {
            $doc->update(['synced_to_sqlserver' => true]);
            return true;
        }

        Log::warning('Push HR Document to SQL Server failed for ID: ' . $doc->id, [
            'errors' => \sqlsrv_errors()
        ]);
        return false;
    }
}

