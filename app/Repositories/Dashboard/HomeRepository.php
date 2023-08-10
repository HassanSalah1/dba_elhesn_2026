<?php

namespace App\Repositories\Dashboard;


use App\Entities\Key;
use App\Models\Setting;
use App\Repositories\General\UtilsRepository;

class HomeRepository
{


    public static function saveAbout(array $data)
    {
        // about ar
        if (isset($data[Key::CITY_DESCRIPTION_AR])) {
            $about_ar = Setting::where(['key' => Key::CITY_DESCRIPTION_AR])->first();
            if ($about_ar) {
                $about_ar->update(['value' => $data[Key::CITY_DESCRIPTION_AR]]);
            } else {
                Setting::create(['key' => Key::CITY_DESCRIPTION_AR, 'value' => $data[Key::CITY_DESCRIPTION_AR]]);
            }
        }

        // about en
        if (isset($data[Key::CITY_DESCRIPTION_EN])) {
            $about_en = Setting::where(['key' => Key::CITY_DESCRIPTION_EN])->first();
            if ($about_en) {
                $about_en->update(['value' => $data[Key::CITY_DESCRIPTION_EN]]);
            } else {
                Setting::create(['key' => Key::CITY_DESCRIPTION_EN, 'value' => $data[Key::CITY_DESCRIPTION_EN]]);
            }
        }

        return UtilsRepository::response(true, trans('admin.process_success_message'), '');
    }


    public static function saveTerms(array $data)
    {
        // terms ar
        if (isset($data[Key::TERMS_AR])) {
            $terms_ar = Setting::where(['key' => Key::TERMS_AR])->first();
            if ($terms_ar) {
                $terms_ar->update(['value' => $data[Key::TERMS_AR]]);
            } else {
                Setting::create(['key' => Key::TERMS_AR, 'value' => $data[Key::TERMS_AR]]);
            }
        }

        // terms en
        if (isset($data[Key::TERMS_EN])) {
            $terms_en = Setting::where(['key' => Key::TERMS_EN])->first();
            if ($terms_en) {
                $terms_en->update(['value' => $data[Key::TERMS_EN]]);
            } else {
                Setting::create(['key' => Key::TERMS_EN, 'value' => $data[Key::TERMS_EN]]);
            }
        }

        return UtilsRepository::response(true, trans('admin.process_success_message')
            , '');
    }

    public static function saveMagles(array $data)
    {
        // terms ar
        if (isset($data[Key::MAGLES_AR])) {
            $terms_ar = Setting::where(['key' => Key::MAGLES_AR])->first();
            if ($terms_ar) {
                $terms_ar->update(['value' => $data[Key::MAGLES_AR]]);
            } else {
                Setting::create(['key' => Key::MAGLES_AR, 'value' => $data[Key::MAGLES_AR]]);
            }
        }

        // terms en
        if (isset($data[Key::MAGLES_EN])) {
            $terms_en = Setting::where(['key' => Key::MAGLES_EN])->first();
            if ($terms_en) {
                $terms_en->update(['value' => $data[Key::MAGLES_EN]]);
            } else {
                Setting::create(['key' => Key::MAGLES_EN, 'value' => $data[Key::MAGLES_EN]]);
            }
        }

        return UtilsRepository::response(true, trans('admin.process_success_message')
            , '');
    }

    public static function saveHistory(array $data)

    {
        // HISTORY_AR
        if (isset($data[Key::CLUB_HISTORY_AR])) {
            $terms_ar = Setting::where(['key' => Key::CLUB_HISTORY_AR])->first();
            if ($terms_ar) {
                $terms_ar->update(['value' => $data[Key::CLUB_HISTORY_AR]]);
            } else {
                Setting::create(['key' => Key::CLUB_HISTORY_AR, 'value' => $data[Key::CLUB_HISTORY_AR]]);
            }
        }

        // HISTORY_EN
        if (isset($data[Key::CLUB_HISTORY_EN])) {
            $terms_en = Setting::where(['key' => Key::CLUB_HISTORY_EN])->first();
            if ($terms_en) {
                $terms_en->update(['value' => $data[Key::CLUB_HISTORY_EN]]);
            } else {
                Setting::create(['key' => Key::CLUB_HISTORY_EN, 'value' => $data[Key::CLUB_HISTORY_EN]]);
            }
        }

        return UtilsRepository::response(true, trans('admin.process_success_message')
            , trans('admin.success_title'));
    }

    public static function saveSetting($data)
    {
        // facebook
        $facebook = Setting::where(['key' => Key::FACEBOOK])->first();
        if ($facebook) {
            $facebook->update([
                'value' => (isset($data[Key::FACEBOOK])) ? $data[Key::FACEBOOK] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::FACEBOOK,
                'value' => (isset($data[Key::FACEBOOK])) ? $data[Key::FACEBOOK] : null
            ]);
        }


        // twitter
        $twitter = Setting::where(['key' => Key::TWITTER])->first();
        if ($twitter) {
            $twitter->update([
                'value' => (isset($data[Key::TWITTER])) ? $data[Key::TWITTER] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::TWITTER,
                'value' => (isset($data[Key::TWITTER])) ? $data[Key::TWITTER] : null
            ]);
        }

        // youtube
        $youtube = Setting::where(['key' => Key::YOUTUBE])->first();
        if ($youtube) {
            $youtube->update([
                'value' => (isset($data[Key::YOUTUBE])) ? $data[Key::YOUTUBE] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::YOUTUBE,
                'value' => (isset($data[Key::YOUTUBE])) ? $data[Key::YOUTUBE] : null
            ]);
        }

        // instagram
        $instagram = Setting::where(['key' => Key::INSTAGRAM])->first();
        if ($instagram) {
            $instagram->update([
                'value' => (isset($data[Key::INSTAGRAM])) ? $data[Key::INSTAGRAM] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::INSTAGRAM,
                'value' => (isset($data[Key::INSTAGRAM])) ? $data[Key::INSTAGRAM] : null
            ]);
        }

        // email
        $email = Setting::where(['key' => Key::EMAIL])->first();
        if ($email) {
            $email->update([
                'value' => (isset($data[Key::EMAIL])) ? $data[Key::EMAIL] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::EMAIL,
                'value' => (isset($data[Key::EMAIL])) ? $data[Key::EMAIL] : null
            ]);
        }

        // phone
        $phone = Setting::where(['key' => Key::PHONE])->first();
        if ($phone) {
            $phone->update([
                'value' => (isset($data[Key::PHONE])) ? $data[Key::PHONE] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::PHONE,
                'value' => (isset($data[Key::PHONE])) ? $data[Key::PHONE] : null
            ]);
        }

        // latitude
        $latitude = Setting::where(['key' => Key::LATITUDE])->first();
        if ($latitude) {
            $latitude->update([
                'value' => (isset($data[Key::LATITUDE])) ? $data[Key::LATITUDE] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::LATITUDE,
                'value' => (isset($data[Key::LATITUDE])) ? $data[Key::LATITUDE] : null
            ]);
        }

        // longitude
        $longitude = Setting::where(['key' => Key::LONGITUDE])->first();
        if ($longitude) {
            $longitude->update([
                'value' => (isset($data[Key::LONGITUDE])) ? $data[Key::LONGITUDE] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::LONGITUDE,
                'value' => (isset($data[Key::LONGITUDE])) ? $data[Key::LONGITUDE] : null
            ]);
        }

        return UtilsRepository::response(true, trans('admin.process_success_message')
            , '');
    }

}

?>
