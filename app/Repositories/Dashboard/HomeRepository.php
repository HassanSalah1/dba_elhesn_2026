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

        if ($data['request']->has(Key::CLUB_STRUCTURE)) {
            $CLUB_STRUCTURE = Setting::where(['key' => Key::CLUB_STRUCTURE])->first();
            $file_id = 'File_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $file_name = Key::CLUB_STRUCTURE;
            $file_path = 'uploads/pdf/';
            $file = UtilsRepository::uploadFiles($data['request'], $file_name, $file_path, $file_id);
            if ($file !== false) {
                if ($CLUB_STRUCTURE) {
                    if (file_exists($CLUB_STRUCTURE->value)) {
                        unlink($CLUB_STRUCTURE->value);
                    }
                    $CLUB_STRUCTURE->update([
                        'value' => $file
                    ]);
                } else {
                    Setting::create([
                        'key' => Key::CLUB_STRUCTURE,
                        'value' => $file
                    ]);
                }
            }
        }

        return UtilsRepository::response(true, trans('admin.process_success_message')
            , '');
    }

    public static function saveElders($data)
    {
        if ($data['request']->has(Key::Image_1)) {
            $image1 = Setting::where(['key' => Key::Image_1])->first();
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $file_name = Key::Image_1;
            $file_path = 'uploads/elders/';
            $file = UtilsRepository::uploadFiles($data['request'], $file_name, $file_path, $file_id);
            if ($file !== false) {
                if ($image1) {
                    if (file_exists($image1->value)) {
                        unlink($image1->value);
                    }
                    $image1->update([
                        'value' => $file
                    ]);
                } else {
                    Setting::create([
                        'key' => Key::Image_1,
                        'value' => $file
                    ]);
                }
            }
        }

        $name_1 = Setting::where(['key' => Key::name_1])->first();
        if ($name_1) {
            $name_1->update([
                'value' => (isset($data[Key::name_1])) ? $data[Key::name_1] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::name_1,
                'value' => (isset($data[Key::name_1])) ? $data[Key::name_1] : null
            ]);
        }

        $position_1 = Setting::where(['key' => Key::position_1])->first();
        if ($position_1) {
            $position_1->update([
                'value' => (isset($data[Key::position_1])) ? $data[Key::position_1] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::position_1,
                'value' => (isset($data[Key::position_1])) ? $data[Key::position_1] : null
            ]);
        }

        /////////////////////////////
        if ($data['request']->has(Key::Image_2)) {
            $image2 = Setting::where(['key' => Key::Image_2])->first();
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $file_name = Key::Image_2;
            $file_path = 'uploads/elders/';
            $file = UtilsRepository::uploadFiles($data['request'], $file_name, $file_path, $file_id);
            if ($file !== false) {
                if ($image2) {
                    if (file_exists($image2->value)) {
                        unlink($image2->value);
                    }
                    $image2->update([
                        'value' => $file
                    ]);
                } else {
                    Setting::create([
                        'key' => Key::Image_2,
                        'value' => $file
                    ]);
                }
            }
        }

        $name_2 = Setting::where(['key' => Key::name_2])->first();
        if ($name_2) {
            $name_2->update([
                'value' => (isset($data[Key::name_2])) ? $data[Key::name_2] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::name_2,
                'value' => (isset($data[Key::name_2])) ? $data[Key::name_2] : null
            ]);
        }

        $position_2 = Setting::where(['key' => Key::position_2])->first();
        if ($position_2) {
            $position_2->update([
                'value' => (isset($data[Key::position_2])) ? $data[Key::position_2] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::position_2,
                'value' => (isset($data[Key::position_2])) ? $data[Key::position_2] : null
            ]);
        }

        /////////////////////////////
        if ($data['request']->has(Key::Image_3)) {
            $image3 = Setting::where(['key' => Key::Image_3])->first();
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $file_name = Key::Image_3;
            $file_path = 'uploads/elders/';
            $file = UtilsRepository::uploadFiles($data['request'], $file_name, $file_path, $file_id);
            if ($file !== false) {
                if ($image3) {
                    if (file_exists($image3->value)) {
                        unlink($image3->value);
                    }
                    $image3->update([
                        'value' => $file
                    ]);
                } else {
                    Setting::create([
                        'key' => Key::Image_3,
                        'value' => $file
                    ]);
                }
            }
        }

        $name_3 = Setting::where(['key' => Key::name_3])->first();
        if ($name_3) {
            $name_3->update([
                'value' => (isset($data[Key::name_3])) ? $data[Key::name_3] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::name_3,
                'value' => (isset($data[Key::name_3])) ? $data[Key::name_3] : null
            ]);
        }

        $position_3 = Setting::where(['key' => Key::position_3])->first();
        if ($position_3) {
            $position_3->update([
                'value' => (isset($data[Key::position_3])) ? $data[Key::position_3] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::position_3,
                'value' => (isset($data[Key::position_3])) ? $data[Key::position_3] : null
            ]);
        }

        /////////////////////////////
        if ($data['request']->has(Key::Image_4)) {
            $image4 = Setting::where(['key' => Key::Image_4])->first();
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $file_name = Key::Image_4;
            $file_path = 'uploads/elders/';
            $file = UtilsRepository::uploadFiles($data['request'], $file_name, $file_path, $file_id);
            if ($file !== false) {
                if ($image4) {
                    if (file_exists($image4->value)) {
                        unlink($image4->value);
                    }
                    $image4->update([
                        'value' => $file
                    ]);
                } else {
                    Setting::create([
                        'key' => Key::Image_4,
                        'value' => $file
                    ]);
                }
            }
        }

        $name_4 = Setting::where(['key' => Key::name_4])->first();
        if ($name_4) {
            $name_4->update([
                'value' => (isset($data[Key::name_4])) ? $data[Key::name_4] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::name_4,
                'value' => (isset($data[Key::name_4])) ? $data[Key::name_4] : null
            ]);
        }

        $position_4 = Setting::where(['key' => Key::position_4])->first();
        if ($position_4) {
            $position_4->update([
                'value' => (isset($data[Key::position_4])) ? $data[Key::position_4] : null
            ]);
        } else {
            Setting::create([
                'key' => Key::position_4,
                'value' => (isset($data[Key::position_4])) ? $data[Key::position_4] : null
            ]);
        }


        return UtilsRepository::response(true, trans('admin.process_success_message')
            , '');
    }

}

?>
