<?php

namespace App\Repositories\Api\User;


use App\Entities\HttpCode;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Models\Notification;
use App\Models\Subscribe;
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
            'user_id' => auth()->id(),
            'player_full_name_ar' => $data['player_full_name_ar'],
            'player_full_name_en' => $data['player_full_name_en'],
            "player_photo" => $data['player_photo'] ?: null,
            'parent_full_name_ar' => $data['parent_full_name_ar'],
            'parent_full_name_en' => $data['parent_full_name_en'],
            'birth_date' => date('Y-m-d', strtotime($data['birth_date'])),
            'nationality' => $data['nationality'],
            'birth_place' => $data['birth_place'],
            'parent_email' => $data['parent_email'],
            'parent_category' => $data['parent_category'],
            'clothes_size' => $data['clothes_size'],
            'shoe_size' => $data['shoe_size'],
            'weight' => $data['weight'],
            'height' => $data['height'],
            "is_another_club" => $data['is_another_club'] == 1 ? 1 : 0,
            "another_club_name" => $data['another_club_name'],
            "parent_passport_photo" => $data['parent_passport_photo'] ?: null,
            "parent_residence_photo" => $data['parent_residence_photo'] ?: null,
            "parent_id_photo" => $data['parent_id_photo'] ?: null,
            'guardian_phone' => $data['guardian_phone']
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
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_name = 'image';
        $image_path = 'uploads/subscribe/';
        $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id, 500, 600);
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
}
