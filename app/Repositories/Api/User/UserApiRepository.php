<?php

namespace App\Repositories\Api\User;


use App\Entities\HttpCode;
use App\Entities\Period;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\SportGameResource;
use App\Http\Resources\TeamPlayerResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTeamResource;
use App\Models\AdministrativeReport;
use App\Models\AdvanceRequest;
use App\Models\Notification;
use App\Models\PresenceAbsence;
use App\Models\PresenceAbsencePlayer;
use App\Models\SportGame;
use App\Models\SportTeam;
use App\Models\Subscribe;
use App\Models\TeamPlayer;
use App\Models\User;
use App\Models\UserTeam;
use App\Repositories\Api\Auth\AuthApiRepository;
use App\Repositories\Api\SqlServerApiRepository;
use App\Repositories\General\UtilsRepository;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;


class UserApiRepository
{


    public static function getProfile(array $data)
    {
        $user = auth()->user();
        return [
            'data' => UserResource::make($user),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function editProfile(array $data)
    {
        $userData = [
            'name' => (isset($data['name'])) ? $data['name'] : $data['user']->name,
            'edited_email' => (isset($data['email']) && $data['email'] !== $data['user']->email) ?
                $data['email'] : null,
            'phone' => isset($data['phone']) ? $data['phone'] : $data['user']->phone,
        ];

        if (isset($data['password']) && !empty($data['password'])) {
            $userData['password'] = $data['password'];
        }
        if ($data['request']->hasFile('image')) {
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/users/';
            $data['image'] = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id);
            if ($data['image'] !== false) {
                if ($data['user']->image !== null && file_exists($data['user']->image)) {
                    unlink($data['user']->image);
                }
                $userData['image'] = $data['image'];
            }
        }

        if ($userData['edited_email'] !== null) {
            $is_sent = AuthApiRepository::sendVerificationCode($data['user']);
        }

        $data['user']->update($userData);
        $data['user']->refresh();
        return [
            'data' => UserResource::make($data['user']),
            'message' => trans('api.done_successfully'),
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function downloadProfileCard(array $data)
    {
        $user = auth()->user();
        return PDF::loadView('pdf.profile_card', [
            'user' => $user
        ])
            ->setPaper('a4', 'landscape')
            ->setWarnings(true)
            ->download($user->name . '.pdf');
    }

    public static function getNotificationsCount(array $data)
    {
        $count = Notification::where(['user_id' => auth()->id(), 'seen' => 0])->count();
        return [
            'data' => $count,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }


    public static function getMyNotifications(array $data)
    {
        $page = (isset($data['page'])) ? $data['page'] : 1;
        $per_page = 20;
        $offset = $per_page * ($page - 1);

        $notifications = Notification::where(['user_id' => auth()->user()->id])
            ->orderBy('id', 'desc');

        $count = $notifications->count();
        $notifications = $notifications->offset($offset)
            ->skip($offset)
            ->take($per_page)
            ->get();

        $notifications = new Paginator(NotificationResource::collection($notifications), $count, $per_page);

        Notification::where(['user_id' => auth()->user()->id])
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->skip($offset)
            ->take($per_page)
            ->update([
                'seen' => 1
            ]);

        return [
            'data' => $notifications,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function subscribeSport(array $data)
    {
        $subscribeData = [
            'user_id' => auth('api')->user() ? auth('api')->id() : null,
            'player_email' => $data['player_email'],
            "player_full_name_ar" => $data['player_full_name_ar'],
            "player_full_name_en" => $data['player_full_name_en'],
            "birth_date" => date('Y-m-d', strtotime($data['birth_date'])),
            "nationality" => $data['nationality'],
            "birth_place" => $data['birth_place'],
            "player_category" => $data['player_category'],
            "player_id_number" => $data['player_id_number'],
            "player_id_expire_date" => date('Y-m-d', strtotime($data['player_id_expire_date'])),
            "player_passport_number" => $data['player_passport_number'],
            "player_passport_expire_date" => date('Y-m-d', strtotime($data['player_passport_expire_date'])),
            "address" => $data['address'],
            "player_phone" => isset($data['player_phone']) ? $data['player_phone'] : null,
            "player_school_name" => isset($data['player_school_name']) ? $data['player_school_name'] : null,
            "player_class_name" => isset($data['player_class_name']) ? $data['player_class_name'] : null,
            "another_club_name" => isset($data['another_club_name']) ? $data['another_club_name'] : null,
            "sport_id" => $data['sport_id'],
            "sport_level" => $data['sport_level'],
            "weight" => $data['weight'],
            "height" => $data['height'],
            "clothes_size" => $data['clothes_size'],
            "shoe_size" => $data['shoe_size'],
            "parent_phone" => $data['parent_phone'],
            "parent_job" => isset($data['parent_job']) ? $data['parent_job'] : null,
            "player_photo" => $data['player_photo'],
            "player_id_photo" => $data['player_id_photo'],
            "player_passport_photo" => $data['player_passport_photo'],
            "player_medical_examination_photo" => $data['player_medical_examination_photo'],
            "player_birth_certificate_photo" => isset($data['player_birth_certificate_photo']) ? $data['player_birth_certificate_photo'] : null,
            "parent_id_photo" => $data['parent_id_photo'],
            "parent_passport_photo" => $data['parent_passport_photo'],
            "parent_residence_photo" => isset($data['parent_residence_photo']) ? $data['parent_residence_photo'] : null,
            "parent_acknowledgment_file" => isset($data['parent_acknowledgment_file']) ? $data['parent_acknowledgment_file'] : null,
            "player_mother_passport_photo" => isset($data['player_mother_passport_photo']) ? $data['player_mother_passport_photo'] : null,
            "player_kafel_passport_photo" => isset($data['player_kafel_passport_photo']) ? $data['player_kafel_passport_photo'] : null,
        ];
        $subscribe = Subscribe::create($subscribeData);
        if ($subscribe) {
            return [
                'message' => trans('api.done_successfully'),
                'code' => HttpCode::SUCCESS
            ];
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function uploadImage(array $data)
    {
        $file_id = 'file_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_name = 'image';
        $image_path = 'uploads/subscribe/';
        $image = UtilsRepository::uploadFiles($data['request'], $image_name, $image_path, $file_id);
        if ($image !== false) {
            return [
                'data' => [
                    'image' => $image,
                    'image_url' => url($image)
                ],
                'message' => 'success',
                'code' => HttpCode::SUCCESS
            ];
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function getMyTeams(array $data)
    {
        $user = auth()->user();
        $teams = UserTeam::where(['user_id' => $user->id])->get();
        return [
            'data' => UserTeamResource::collection($teams),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function administrativeReport(array $data)
    {
        $user = auth()->user();
        $userTeam = UserTeam::find($data['team_id']);
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "INSERT INTO dbo.tblOfficial_Actions (OfficialID,UserID,InsertedDateTime,Topic,ActionDate,ActionPlace,TheEvents,Negativity,Positivity,Recommendations) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $userTeam->official_id,
                $user->user_id,
                date('Y-m-d H:i:s'),
                $data['subject'], date('Y-m-d H:i:s', strtotime($data['date'])),
                isset($data['location']) ? $data['location'] : null,
                isset($data['events']) ? $data['events'] : null,
                isset($data['cons']) ? $data['cons'] : null,
                isset($data['pros']) ? $data['pros'] : null,
                isset($data['recommendations']) ? $data['recommendations'] : null,
            ];
            $stmt = sqlsrv_prepare($conn, $sql, $params);
            $execute = sqlsrv_execute($stmt);
            sqlsrv_close($conn);
            if ($execute) {
                if ($userTeam->team->email) {
                    UtilsRepository::sendReportEmail('تقرير الإدارى:' . $userTeam->team->name,
                        $userTeam->team->email);
                }
                return [
                    'message' => trans('api.success_message'),
                    'code' => HttpCode::SUCCESS
                ];
            }
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function advanceRequests(array $data)
    {
        $user = auth()->user();

        $userTeam = UserTeam::find($data['team_id']);

        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "INSERT INTO dbo.tbl_RequestRelease (TeamRowID,Players,Officials,TheCost,Details,WhoInsert,WhenInsert,Match,TheDate,Place,MatchTime,LeaveTime,ReturnTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $details = '';
            if (isset($data['breakfast']) && intval($data['breakfast']) > 0) {
                $details .= 'فطار';
            }
            if (isset($data['snacks']) && intval($data['snacks']) > 0) {
                if (isset($data['breakfast']) && intval($data['breakfast']) > 0) {
                    $details .= ' + ';
                }
                $details .= 'سناكس';
            }
            if (isset($data['lunch']) && intval($data['lunch']) > 0) {
                if (isset($data['snacks']) && intval($data['snacks']) > 0) {
                    $details .= ' + ';
                }
                $details .= 'غداء';
            }
            if (isset($data['dinner']) && intval($data['dinner']) > 0) {
                if (isset($data['lunch']) && intval($data['lunch']) > 0) {
                    $details .= ' + ';
                }
                $details .= 'عشاء';
            }

            $params = [
                $userTeam->team->team_id,
                $data['players_count'],
                $data['escorts_count'],
                $data['cost'],
                $details,
                $user->user_id,
                date('Y-m-d H:i:s'),
                isset($data['tournament']) ? $data['tournament'] : null,
                isset($data['date']) ? date('Y-m-d H:i:s', strtotime($data['date'])) : null,
                isset($data['location']) ? $data['location'] : null,
                isset($data['match_timing']) ? $data['match_timing'] : null,
                isset($data['move_date']) ? $data['move_date'] : null,
                isset($data['return_date']) ? $data['return_date'] : null,
            ];
            $stmt = sqlsrv_prepare($conn, $sql, $params);
            $execute = sqlsrv_execute($stmt);
            sqlsrv_close($conn);
            if ($execute) {
                if ($userTeam->team->email) {
                    UtilsRepository::sendReportEmail('طلب سلفة:' . $userTeam->team->name,
                        $userTeam->team->email);
                }
                return [
                    'message' => trans('api.success_message'),
                    'code' => HttpCode::SUCCESS
                ];
            }
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }


    public static function getTeamPlayers(array $data)
    {
        $team = UserTeam::where(['id' => $data['id']])->first();
        $team = SportTeam::find($team ? $team->team_id : null);
        $players = TeamPlayer::where(['team_id' => $team ? $team->team_id : null])
//            ->select('player_id' , 'image' , 'name_'.App::getLocale().' AS name')
//            ->orderBy('name' , 'ASC')
            ->get();
        return [
            'data' => array_values(collect(TeamPlayerResource::collection($players))->sortBy('name')->toArray()),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getReasons(array $data)
    {
        $conn = SqlServerApiRepository::startConnection();
        $resultData = [];
        if ($conn) {
            $sql = "SELECT ReasonKey AS id, TheReason AS reason FROM dbo.tbl_Attend_Reasons ORDER BY TheOrder ASC";
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    $resultData[] = [
                        'id' => $object->id,
                        'reason' => $object->reason,
                    ];
                }
            }
            sqlsrv_close($conn);
        }

        return [
            'data' => $resultData,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function presenceAbsence(array $data)
    {
        $user = auth()->user();

        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            if (is_array($data['players'])) {
                $execute = false;
                foreach ($data['players'] as $key => $player) {
                    $sql = "INSERT INTO dbo.tbl_Players_Attendance (SeasonTeamPlayerRowID,ReasonKey,TheDate,PlayerRowID,UserID,WhenInserted,Comments,Relief,Visit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $visit = '';
                    if ($data['period'] === Period::one_period_day) {
                        $visit = 'يوم فترة واحدة';
                    } else if ($data['period'] === Period::evening_another_period) {
                        $visit = 'مسائى فترة تانيه';
                    } else if ($data['period'] === Period::my_first_morning) {
                        $visit = 'صباحى فترة اولى';
                    }
                    $params = [
                        SqlServerApiRepository::getSeasonTeamPlayerId($conn, $player['player_id']),
                        $player['attendance_status'],
                        date('Y-m-d H:i:s', strtotime($data['date'])),
                        $player['player_id'],
                        $user->user_id,
                        date('Y-m-d H:i:s'),
                        $player['notes'],
                        null,
                        $visit
                    ];
                    $stmt = sqlsrv_prepare($conn, $sql, $params);
                    $execute = sqlsrv_execute($stmt);
                }
                sqlsrv_close($conn);
                if ($execute) {
                    $playerObj = TeamPlayer::where(['player_id' => $data['players'][0]['player_id']])->first();
                    if ($playerObj->team->email) {
                        UtilsRepository::sendReportEmail('تقييم العام للاعبين:' . $playerObj->name,
                            $playerObj->team->email);
                    }
                    return [
                        'message' => trans('api.success_message'),
                        'code' => HttpCode::SUCCESS
                    ];
                }
            }
        }

        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function generalEvaluation(array $data)
    {
        $user = auth()->user();
        $userTeam = UserTeam::find($data['team_id']);

        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "INSERT INTO dbo.tbl_Evaluations_Global (TeamRowID,PlayerRowID,SeasonTeamPlayerRowID,UserID,Eval_DateTime,Position,Behavior,Commitment,Technical,Physical,Participations,Recommendation,Comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $userTeam->team->team_id,
                $data['player_id'],
                SqlServerApiRepository::getSeasonTeamPlayerId($conn, $data['player_id']),
                $user->user_id,
                date('Y-m-d H:i:s'),
                isset($data['position']) ? $data['position'] : null,
                isset($data['behavior']) ? $data['behavior'] : null,
                isset($data['commitment']) ? $data['commitment'] : null,
                isset($data['technical']) ? $data['technical'] : null,
                isset($data['physical']) ? $data['physical'] : null,
                isset($data['participations']) ? $data['participations'] : null,
                isset($data['recommendation']) ? $data['recommendation'] : null,
                isset($data['comments']) ? $data['comments'] : null,
            ];
            $stmt = sqlsrv_prepare($conn, $sql, $params);
            $execute = sqlsrv_execute($stmt);
            sqlsrv_close($conn);
            if ($execute) {
                if ($userTeam->team->email) {
                    UtilsRepository::sendReportEmail('تقييم العام للاعبين:' . $userTeam->team->name,
                        $userTeam->team->email);
                }
                return [
                    'message' => trans('api.success_message'),
                    'code' => HttpCode::SUCCESS
                ];
            }
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function coachEvaluation(array $data)
    {
        $user = auth()->user();
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            $sql = "INSERT INTO dbo.tbl_Coach_Match_Evaluation_OG (Season,SportID,Category,Comp,TheDate,Participants,Difficulty,Results,Team_Performance,Weakness,Strength,Abbsents,Injuries,Comments,UserID,InsertedDateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?)";

            $params = [
                $data['season'],
                $data['sport_id'],
                $data['category'],
                $data['comp'],
                date('Y-m-d H:i:s', strtotime($data['date'])),
                $data['participants'],
                $data['difficulty'],
                isset($data['results']) ? $data['results'] : null,
                isset($data['team_performance']) ? $data['team_performance'] : null,
                isset($data['weakness']) ? $data['weakness'] : null,
                isset($data['strength']) ? $data['strength'] : null,
                isset($data['abbsents']) ? $data['abbsents'] : null,
                isset($data['injuries']) ? $data['injuries'] : null,
                isset($data['comments']) ? $data['comments'] : null,
                $user->user_id,
                date('Y-m-d H:i:s')
            ];
            $stmt = sqlsrv_prepare($conn, $sql, $params);
            $execute = sqlsrv_execute($stmt);
            sqlsrv_close($conn);
            if ($execute) {
                return [
                    'message' => trans('api.success_message'),
                    'code' => HttpCode::SUCCESS
                ];
            }
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }

    public static function getSports(array $data)
    {
        $games = SportGame::orderBy('order', 'ASC')
            ->select('game_id AS id', 'title_ar', 'title_en', 'description_ar', 'description_en',
                'image', 'order')
            ->get();
        $games = SportGameResource::collection($games);
        // return success response
        return [
            'data' => $games,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getSeasons(array $data)
    {
        $conn = SqlServerApiRepository::startConnection();
        $resultData = [];
        if ($conn) {
            $sql = "SELECT SName AS season FROM dbo.tblSeasons";
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    $resultData[] = [
                        'season' => $object->season,
                    ];
                }
            }
            sqlsrv_close($conn);
        }

        return [
            'data' => $resultData,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getCompetitions(array $data)
    {
        $conn = SqlServerApiRepository::startConnection();
        $resultData = [];
        if ($conn) {
            $sql = "SELECT * FROM dbo.MobileApp_Competition_Sport_" . $data['id'];
            if (($result = \sqlsrv_query($conn, $sql)) !== false) {
                $id = intval($data['id']);
                while ($object = sqlsrv_fetch_object($result)) {
                    if (in_array($id, [1,2, 7, 9])) {
                        $objectArr = [
                            'name' => $object->CompetitionAR . '(' . $object->HomeAR . '-' . $object->AgainstAR . ')',
                            'name_en' => $object->CompetitionEN . '(' . $object->HomeEN . '-' . $object->AgainstEN . ')',
                            'date' => $object->Date_and_Time,
                            'result' => $object->Result,
                        ];
                    } else if (in_array($id, [4, 5,6, 8])) {
                        $objectArr = [
                            'name' => $object->CompetitionAR,
                            'name_en' => $object->CompetitionEN,
                            'date' => (new \DateTime($object->DateFrom))->format('Y-m-d') .'-'
                                .(new \DateTime($object->DateTo))->format('Y-m-d') ,
                            'result' => $object->Rank,
                        ];
                    } else {
                        $objectArr = [
                            'name' => $object->CompetitionAR,
                            'name_en' => $object->CompetitionEN,
                            'date' => date('Y-m-d' , strtotime($object->TheDate->date)),
                            'result' => $object->Rank,
                        ];
                    }
                    $resultData[] = $objectArr;
                }
            }
            sqlsrv_close($conn);
        }

        return [
            'data' => $resultData,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getMatches(array $data)
    {
        $conn = SqlServerApiRepository::startConnection();
        $resultData = [];
        if ($conn) {
            $sql = "SELECT * FROM dbo.tblMatches WHERE Matchdate='" . date('Y-m-d') . "'";
            $result = \sqlsrv_query($conn, $sql);
            if ($result !== false) {
                while ($object = sqlsrv_fetch_object($result)) {
                    $resultData[] = $object;
                }
            }
            sqlsrv_close($conn);
        }

        return [
            'data' => $resultData,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function updateMatcheResult(array $data)
    {
        $conn = SqlServerApiRepository::startConnection();
        if ($conn) {
            foreach ($data['matches'] as $match) {
                $params = [
                    $match['result1'],
                    $match['result2'],
                    $match['id']
                ];
                $sql = "UPDATE dbo.tblMatches SET Team1Result=? , Team2Result=? WHERE RowID=?";
                $stmt = sqlsrv_prepare($conn, $sql, $params);
                $execute = sqlsrv_execute($stmt);
            }
            sqlsrv_close($conn);
            return [
                'message' => trans('api.success_message'),
                'code' => HttpCode::SUCCESS
            ];
        }
        return [
            'message' => trans('api.general_error_message'),
            'code' => HttpCode::ERROR
        ];
    }
}
