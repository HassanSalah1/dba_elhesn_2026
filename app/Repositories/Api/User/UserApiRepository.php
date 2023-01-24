<?php

namespace App\Repositories\Api\User;


use App\Entities\HttpCode;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTeamResource;
use App\Models\AdministrativeReport;
use App\Models\AdvanceRequest;
use App\Models\Notification;
use App\Models\Subscribe;
use App\Models\UserTeam;
use App\Repositories\Api\Auth\AuthApiRepository;
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
        $created = AdministrativeReport::create([
            'user_team_id' => $data['team_id'],
            'user_id' => $user->id,
            'date' => date('Y-m-d', strtotime($data['date'])),
            'subject' => $data['subject'],
            'events' => isset($data['events']) ? $data['events'] : null,
            'pros' => isset($data['pros']) ? $data['pros'] : null,
            'cons' => isset($data['cons']) ? $data['cons'] : null,
            'recommendations' => isset($data['recommendations']) ? $data['recommendations'] : null,
            'location' => isset($data['location']) ? $data['location'] : null
        ]);

        if ($created) {
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

    public static function advanceRequests(array $data)
    {
        $user = auth()->user();
        $created = AdvanceRequest::create([
            'user_team_id' => $data['team_id'],
            'user_id' => $user->id,
            'players_count' => $data['players_count'],
            'escorts_count' => $data['escorts_count'],
            'cost' => $data['cost'],
            'location' => isset($data['location']) ? $data['location'] : null,
            'statement' => isset($data['statement']) ? $data['statement'] : null,
            'tournament' => isset($data['tournament']) ? $data['tournament'] : null,
            'match_timing' => isset($data['match_timing']) ? $data['match_timing'] : null,
            'move_date' => isset($data['move_date']) ? $data['move_date'] : null,
            'return_date' => isset($data['return_date']) ? $data['return_date'] : null,
            'breakfast' => isset($data['breakfast']) ? $data['breakfast'] : null,
            'lunch' => isset($data['lunch']) ? $data['lunch'] : null,
            'dinner' => isset($data['dinner']) ? $data['dinner'] : null,
            'snacks' => isset($data['snacks']) ? $data['snacks'] : null,
        ]);

        if ($created) {
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
