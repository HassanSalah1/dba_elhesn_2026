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
}
