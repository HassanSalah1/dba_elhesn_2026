<?php

namespace App\Repositories\Api;

use App\Entities\HttpCode;
use App\Entities\ImageType;
use App\Entities\Key;
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
use App\Repositories\General\UtilsRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class SqlServerApiRepository
{


    public static function startConnection()
    {
        $serverName = 'dhsckarem.ddns.net';
        $uid = 'dhclubapp';
        $pwd = 'bNHW^3&q1mH5';
        $databaseName = 'FBall';

        $connectionInfo = [
            "UID" => $uid,
            "PWD" => $pwd,
            "Database" => $databaseName,
            "ColumnEncryption" => "Enabled",
            "TrustServerCertificate" => true,
            "CharacterSet" => "UTF-8"
        ];
        /* Connect using SQL Server Authentication. */
        $conn = \sqlsrv_connect($serverName, $connectionInfo);
        if ($conn) {
            return $conn;
        }
        return false;
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
            $sql = "SELECT SportID, TeamAR, TeamEN, TeamRowID FROM dbo.MobileApp_Teams";
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    SportTeam::updateOrCreate([
                        'team_id' => $object->TeamRowID
                    ], [
                        'sport_id' => $object->SportID,
                        'name_en' => $object->TeamEN,
                        'name_ar' => $object->TeamAR,
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
            $sql = "SELECT TeamRowID, PNameAR, PNameEN, PlayerRowID FROM dbo.MobileApp_Players ";
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    TeamPlayer::updateOrCreate([
                        'player_id' => $object->PlayerRowID
                    ], [
                        'team_id' => $object->TeamRowID,
                        'name_en' => $object->PNameAR,
                        'name_ar' => $object->PNameEN,
                        'image' => null
                    ]);
                }
            }
            sqlsrv_close($conn);
        }
    }

    public static function getPlayerImage($conn, $playerId)
    {
        $sql = "SELECT TOP 1 PlayerPhoto FROM dbo.MobileApp_PlayersPhotos WHERE PlayerRowID=$playerId";
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_path = 'uploads/players/';
        if (($result = \sqlsrv_query($conn, $sql)) !== false) {
            $object = sqlsrv_fetch_object($result);
            if ($object) {
                return UtilsRepository::createImageBase64(base64_encode($object->PlayerPhoto), $image_path, $file_id, 282, 561);
            }
        }
        return null;
    }

    public static function getPlayerImages()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $players = TeamPlayer::where('image', '=', null)->get();
            foreach ($players as $player) {
                $player->update([
                    'image' => self::getPlayerImage($conn, $player->player_id)
                ]);
            }
        }
    }

    public static function getTeamImages()
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $teams = SportTeam::where('image', '=', null)->get();
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


}
