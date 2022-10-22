<?php

namespace App\Repositories\Api\User;


use App\Entities\HttpCode;
use App\Http\Resources\UserResource;
use App\Repositories\Api\Auth\AuthApiRepository;
use App\Repositories\General\UtilsRepository;
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
        return PDF::loadView('pdf.profile_card' , [
            'user' => $user
        ])
            ->setPaper('a4', 'landscape')
            ->setWarnings(true)
            ->download($user->name . '.pdf');
    }
}
