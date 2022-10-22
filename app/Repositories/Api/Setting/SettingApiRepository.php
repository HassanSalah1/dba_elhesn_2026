<?php

namespace App\Repositories\Api\Setting;

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
use App\Models\Regulations;
use App\Models\Setting;
use App\Models\SportGame;
use App\Models\Team;
use App\Repositories\General\UtilsRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class SettingApiRepository
{


    public static function getAbout(array $data)
    {

        $lang = App::getLocale();
        $setting = Setting::where(['key' => ($lang === 'en') ?
            Key::CITY_DESCRIPTION_EN : Key::CITY_DESCRIPTION_AR])->first();

        // return success response
        return [
            'data' => [
                'city_description' => $setting ? $setting->value : null,
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getTerms(array $data)
    {

        $lang = App::getLocale();
        $setting = Setting::where(['key' => ($lang === 'en') ?
            Key::TERMS_EN : Key::TERMS_AR])->first();
        // return success response
        return [
            'data' => [
                'terms' => $setting ? $setting->value : null
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getHistory(array $data)
    {

        $lang = App::getLocale();
        $setting = Setting::where(['key' => ($lang === 'en') ?
            Key::CLUB_HISTORY_EN : Key::CLUB_HISTORY_AR])->first();

        // return success response
        return [
            'data' => [
                'history' => $setting ? $setting->value : null
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getContactDetails(array $data)
    {

        $phone = Setting::where(['key' => Key::PHONE])->first();
        $latitude = Setting::where(['key' => Key::LATITUDE])->first();
        $longitude = Setting::where(['key' => Key::LONGITUDE])->first();

        $email = Setting::where(['key' => Key::EMAIL])->first();
        $facebook = Setting::where(['key' => Key::FACEBOOK])->first();
        $twitter = Setting::where(['key' => Key::TWITTER])->first();
        $instagram = Setting::where(['key' => Key::INSTAGRAM])->first();
        $youtube = Setting::where(['key' => Key::YOUTUBE])->first();

        // return success response
        return [
            'data' => [
                'phone' => $phone ? $phone->value : null,
                'email' => $email ? $email->value : null,
                'latitude' => $latitude ? (float)$latitude->value : null,
                'longitude' => $longitude ? (float)$longitude->value : null,
                'facebook' => $facebook ? $facebook->value : null,
                'twitter' => $twitter ? $twitter->value : null,
                'instagram' => $instagram ? $instagram->value : null,
                'youtube' => $youtube ? $youtube->value : null,
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function addContact(array $data)
    {
        $created = Contact::create([
            'user_id' => optional(auth('api')->user())->id ?: null,
            'contact_type' => $data['contact_type'],
            'message' => $data['message'],
            'name' => isset($data['name']) ? $data['name'] : null,
            'phone' => isset($data['phone']) ? $data['phone'] : null,
        ]);
        if ($created) {
            return [
                'message' => trans('api.done_successfully'),
                'code' => HttpCode::SUCCESS
            ];
        } else {
            return [
                'message' => trans('api.general_error_message'),
                'code' => HttpCode::ERROR
            ];
        }
    }


    public static function getCategories(array $data)
    {
        $categories = Category::all();
        // return success response
        return [
            'data' => CategoryResource::collection($categories),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getTeams(array $data)
    {
        $teams = Team::orderBy('order', 'ASC')->get();
        // return success response
        return [
            'data' => [
                'teams' => TeamResource::collection($teams)
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }


    public static function getGallery(array $data)
    {
        $galleries = Gallery::where(function ($query) use ($data) {
            if (isset($data['type']) && $data['type'] === 'video') {
                $query->where('video_url', '!=', null);
            } else if (isset($data['type']) && $data['type'] === 'image') {
                $query->where('image', '!=', null);
            }
        })->where(['sport_game_id' => null])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        $galleries->{'galleries'} = GalleryResource::collection($galleries);
        // return success response
        return [
            'data' => $galleries,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }


    public static function getSportGames(array $data)
    {
        $games = SportGame::orderBy('order', 'ASC')->paginate(30);
        $games->{'games'} = SportGameResource::collection($games);
        // return success response
        return [
            'data' => $games,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }


    public static function getSportGamesGallery(array $data)
    {
        $galleries = Gallery::where(function ($query) use ($data) {
            if (isset($data['type']) && $data['type'] === 'video') {
                $query->where('video_url', '!=', null);
            } else if (isset($data['type']) && $data['type'] === 'image') {
                $query->where('image', '!=', null);
            }
        })->where(['sport_game_id' => $data['id']])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        $galleries->{'galleries'} = GalleryResource::collection($galleries);
        // return success response
        return [
            'data' => $galleries,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getCommittees(array $data)
    {
        $committees = Committee::orderBy('order', 'ASC')->paginate(10);
        $committees->{'committees'} = CommitteeResource::collection($committees);
        // return success response
        return [
            'data' => $committees,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getHome(array $data)
    {
        $news = News::orderBy('id', 'DESC')
            ->limit(5)
            ->get();
        $news = NewResource::collection($news);
        // return success response
        return [
            'data' => [
                'news' => $news,
                'match' => []
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getNews(array $data)
    {
        $news = News::where(function ($query) use ($data) {
            if (isset($data['keyword'])) {
                $query->where('title_ar', 'LIKE', '%' . $data['keyword'] . '%');
                $query->orWhere('title_en', 'LIKE', '%' . $data['keyword'] . '%');
            }
            if (isset($data['category_id'])) {
                $query->where('category_id', $data['category_id']);
            }
        })
            ->orderBy('id', 'DESC')
            ->paginate(10);
        $news->{'news'} = NewResource::collection($news);
        // return success response
        return [
            'data' => $news,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getNewDetails(array $data)
    {
        $new = News::find($data['id']);
        if (!$new) return [
            'message' => 'not found',
            'code' => HttpCode::ERROR
        ];
        // return success response
        return [
            'data' => NewDetailsResource::make($new),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getActions(array $data)
    {
        $actions = Action::where(function ($query) use ($data) {
            if (isset($data['keyword'])) {
                $query->where('title_ar', 'LIKE', '%' . $data['keyword'] . '%');
                $query->orWhere('title_en', 'LIKE', '%' . $data['keyword'] . '%');
            }
        })
            ->orderBy('start_date', 'DESC')
            ->paginate(10);
        $actions->{'actions'} = ActionResource::collection($actions);
        // return success response
        return [
            'data' => $actions,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getActionDetails(array $data)
    {
        $action = Action::find($data['id']);
        if (!$action) return [
            'message' => 'not found',
            'code' => HttpCode::ERROR
        ];
        // return success response
        return [
            'data' => ActionDetailsResource::make($action),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getIntros(array $data)
    {
        $intros = Intro::orderBy('order', 'ASC')->get();
        return [
            'data' => IntroResource::collection($intros),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getSiteNews(array $data)
    {
        for ($i = 40; $i > 0; $i--) {
            $url = 'https://dhclub.ae/wp-json/wp/v2/posts?page=' . $i;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);

            $news = $server_output ? json_decode($server_output) : [];
            if (!isset($news->code)) {
                if ($news && is_array($news)) {
                    $news = array_reverse($news);
                    foreach ($news as $new) {
                        $newObject = News::where(['new_id' => $new->id])->first();
                        if (!$newObject) {
                            $category = Category::find($new->categories[0]);
                            $newData = [
                                'title_ar' => $new->title->rendered,
                                'title_en' => $new->title->rendered,
                                'short_description_ar' => $new->title->rendered,
                                'short_description_en' => $new->title->rendered,
                                'description_ar' => $new->content->rendered,
                                'description_en' => $new->content->rendered,
                                'video_url' => null,
                                'category_id' => $category ? $category->id : null,
                                'new_id' => $new->id,
                                'created_at' => date('Y-m-d H:i:s', strtotime($new->date))
                            ];
                            $newObject = News::create($newData);

                            $media_url = 'https://dhclub.ae/wp-json/wp/v2/media?parent=' . $new->id;

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $media_url);
                            curl_setopt($ch, CURLOPT_POST, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $output = curl_exec($ch);
                            curl_close($ch);
                            $images = $output ? json_decode($output) : [];
                            if ($images && is_array($images)) {
                                foreach ($images as $key => $image) {
                                    $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
                                    $image_path = 'uploads/news/';
                                    $image = UtilsRepository::uploadImage(null, $image->guid->rendered,
                                        $image_path, $file_id);
                                    if ($image) {
                                        Image::create([
                                            'item_id' => $newObject->id,
                                            'item_type' => ImageType::NEWS,
                                            'image' => $image,
                                            'primary' => $key === 0 ? 1 : 0
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return [
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getRegulations(array $data)
    {
        $intros = Regulations::orderBy('order', 'ASC')->get();
        return [
            'data' => RegulationResource::collection($intros),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }
}
