<?php
 
namespace App\Repositories\Api\V2\Setting;
 
use App\Entities\HttpCode;
use App\Entities\ImageType;
use App\Entities\Key;
use App\Http\Resources\ActionDetailsResource;
use App\Http\Resources\ActionResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CommitteeResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\IntroResource;
use App\Http\Resources\NewDetailsResource;
use App\Http\Resources\NewResource;
use App\Http\Resources\RegulationResource;
use App\Http\Resources\V2\SportGameResource;
use App\Http\Resources\SportTeamResource;
use App\Http\Resources\TeamPlayerResource;
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
use App\Models\SportTeam;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Repositories\General\UtilsRepository;
use Illuminate\Support\Facades\App;
 
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
 
    public static function getMagles(array $data)
    {
 
        $lang = App::getLocale();
        $setting = Setting::where(['key' => ($lang === 'en') ?
            Key::MAGLES_EN : Key::MAGLES_AR])->first();
 
        // return success response
        return [
            'data' => [
                'magles' => $setting ? $setting->value : null,
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }
 
    public static function getClubStructure(array $data)
    {
        $setting = Setting::where(['key' => Key::CLUB_STRUCTURE])->first();
 
        // return success response
        return [
            'data' => [
                'pdf' => $setting ? url($setting->value) : null,
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
 
    public static function getSportTeams(array $data)
    {
        $sport = SportGame::find($data['id']);
        if ($sport) {
            $teams = SportTeam::where(['sport_id' => $sport->game_id])->get();
            return [
                'data' => SportTeamResource::collection($teams),
                'message' => 'success',
                'code' => HttpCode::SUCCESS
            ];
        }
 
        return [
            'message' => 'not found',
            'code' => HttpCode::ERROR
        ];
    }
 
    public static function getTeamPlayers(array $data)
    {
        $team = SportTeam::find($data['id']);
        if ($team) {
            $players = TeamPlayer::where(['team_id' => $team->team_id])->get();
 
            $sport = SportGame::where(['game_id' => $team->sport_id])->first();
            TeamPlayerResource::using([
                'description' => $sport ? $sport->description : ''
            ]);
            return [
                'data' => TeamPlayerResource::collection($players),
                'message' => 'success',
                'code' => HttpCode::SUCCESS
            ];
        }
 
        return [
            'message' => 'not found',
            'code' => HttpCode::ERROR
        ];
    }
 
    public static function getElders(array $data)
    {
        $setting_image1 = Setting::where(['key' => Key::Image_1])->first();
        $setting_name1 = Setting::where(['key' => Key::name_1])->first();
        $setting_position1 = Setting::where(['key' => Key::position_1])->first();
        $setting_image2 = Setting::where(['key' => Key::Image_2])->first();
        $setting_name2 = Setting::where(['key' => Key::name_2])->first();
        $setting_position2 = Setting::where(['key' => Key::position_2])->first();
        $setting_image3 = Setting::where(['key' => Key::Image_3])->first();
        $setting_name3 = Setting::where(['key' => Key::name_3])->first();
        $setting_position3 = Setting::where(['key' => Key::position_3])->first();
        $setting_image4 = Setting::where(['key' => Key::Image_4])->first();
        $setting_name4 = Setting::where(['key' => Key::name_4])->first();
        $setting_position4 = Setting::where(['key' => Key::position_4])->first();
 
        // return success response
        return [
            'data' => [
                [
                    'image' => $setting_image1 ? url($setting_image1->value) : null,
                    'name' => $setting_name1 ? $setting_name1->value : null,
                    'position' => $setting_position1 ? $setting_position1->value : null,
                ],
                [
                    'image' => $setting_image2 ? url($setting_image2->value) : null,
                    'name' => $setting_name2 ? $setting_name2->value : null,
                    'position' => $setting_position2 ? $setting_position2->value : null,
                ],
                [
                    'image' => $setting_image3 ? url($setting_image3->value) : null,
                    'name' => $setting_name3 ? $setting_name3->value : null,
                    'position' => $setting_position3 ? $setting_position3->value : null,
                ],
                [
                    'image' => $setting_image4 ? url($setting_image4->value) : null,
                    'name' => $setting_name4 ? $setting_name4->value : null,
                    'position' => $setting_position4 ? $setting_position4->value : null,
                ]
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }
 
    public static function getEmergencies(array $data)
    {
        $emergencies = \App\Models\Emergency::orderBy('order', 'ASC')->get();
        return [
            'data' => \App\Http\Resources\EmergencyResource::collection($emergencies),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }
 
    public static function getTeamPlayerDetails(array $data)
    {
        $player = TeamPlayer::where('player_id', $data['id'])->first();
        if (!$player) {
            return [
                'message' => 'not found',
                'code' => HttpCode::ERROR
            ];
        }
        return [
            'data' => \App\Http\Resources\TeamPlayerDetailsResource::make($player),
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }
 
    public static function getClinicStatus(array $data)
    {
        $setting = Setting::where(['key' => Key::SHOW_CLINIC])->first();
        $showClinic = $setting ? (int)$setting->value : 0;
 
        return [
            'data' => [
                'show_clinic' => $showClinic
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getSeasons(array $data)
    {
        $seasons = \App\Models\Season::orderBy('name', 'desc')->get();
        return [
            'data' => $seasons,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getClubs(array $data)
    {
        $clubs = \App\Models\Club::orderBy('name_ar', 'asc')->get();
        
        $result = [];
        foreach ($clubs as $club) {
            $result[] = [
                'id' => $club->row_id,
                'name_ar' => $club->name_ar,
                'name_en' => $club->name_en,
                'logo' => $club->logo ? url($club->logo) : url('images/default-logo.png'),
            ];
        }
        
        return [
            'data' => $result,
            'message' => 'success',
            'code' => HttpCode::SUCCESS
        ];
    }

    public static function getMatchesGrouped(array $data)
    {
        $lang = \Illuminate\Support\Facades\App::getLocale();
        $type = isset($data['type']) ? $data['type'] : 'today';
        $todayStr = date('Y-m-d');
        
        $query = \App\Models\Competition::with(['matches' => function ($q) use ($type, $todayStr) {
            if ($type === 'previous') {
                $q->where('match_date', '<', $todayStr)->orderBy('match_date', 'desc');
            } elseif ($type === 'week') {
                $today = \Carbon\Carbon::today();
                $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
                $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
                $q->whereBetween('match_date', [$startOfWeek, $endOfWeek])->orderBy('match_date', 'asc');
            } elseif ($type === 'upcoming' || $type === 'next') {
                $q->where('match_date', '>', $todayStr)->orderBy('match_date', 'asc');
            } else {
                $q->where('match_date', '=', $todayStr)->orderBy('match_date', 'asc');
            }
            $q->with(['team1Club', 'team2Club']);
        }, 'season']);

        if (isset($data['competition_id']) && !empty($data['competition_id'])) {
            $query->where('row_id', $data['competition_id']);
        }
        
        if (isset($data['season_id']) && !empty($data['season_id'])) {
            $query->where('season_row_id', $data['season_id']);
        }

        // Only return competitions that have matches matching the filter
        $query->whereHas('matches', function ($q) use ($type, $todayStr) {
            if ($type === 'previous') {
                $q->where('match_date', '<', $todayStr);
            } elseif ($type === 'week') {
                $today = \Carbon\Carbon::today();
                $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
                $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
                $q->whereBetween('match_date', [$startOfWeek, $endOfWeek]);
            } elseif ($type === 'upcoming' || $type === 'next') {
                $q->where('match_date', '>', $todayStr);
            } else {
                $q->where('match_date', '=', $todayStr);
            }
        });

        $page = isset($data['page']) ? max(1, (int)$data['page']) : 1;
        $pageSize = isset($data['page_size']) ? max(1, (int)$data['page_size']) : 10;
        
        $paginator = $query->paginate($pageSize, ['*'], 'page', $page);
        
        $competitionsData = [];
        foreach ($paginator->items() as $comp) {
            $matchesData = [];
            $count = 0;
            foreach ($comp->matches as $match) {
                if ($count >= 10) break; // Limit to 10 matches per competition in the grouped view
                $count++;
                
                // Determine logo URLs
                $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
                $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
                
                // Fallbacks if null
                if (!$team1Logo) {
                    $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                    if ($club1 && $club1->logo) $team1Logo = url($club1->logo);
                    else {
                        $team1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                        $team1Logo = $team1 && $team1->image ? url($team1->image) : url('images/default-logo.png');
                    }
                }
                
                if (!$team2Logo) {
                    $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                    if ($club2 && $club2->logo) $team2Logo = url($club2->logo);
                    else {
                        $team2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                        $team2Logo = $team2 && $team2->image ? url($team2->image) : url('images/default-logo.png');
                    }
                }

                $matchesData[] = [
                    'id' => $match->row_id,
                    'team1' => $match->team1,
                    'team1_logo' => $team1Logo,
                    'team2' => $match->team2,
                    'team2_logo' => $team2Logo,
                    'team1_result' => $match->team1_result,
                    'team2_result' => $match->team2_result,
                    'match_date' => $match->match_date,
                    'match_time' => $match->match_time,
                    'stage_round' => $match->stage_round,
                    'pitch' => $match->pitch,
                    'week' => $match->week,
                    'live_link' => $match->live_link,
                    'fanet_match_id' => $match->fanet_match_id,
                ];
            }
            
            $competitionsData[] = [
                'id' => $comp->row_id,
                'name' => $lang == 'ar' ? $comp->name_ar : $comp->name_en,
                'logo' => $comp->logo ? url($comp->logo) : null,
                'season' => $comp->season ? $comp->season->name : null,
                'matches' => $matchesData,
                'matches_count' => $comp->matches->count(),
            ];
        }

        return [
            'data' => [
                'competitions' => $competitionsData,
                'total_competitions' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'page_size' => $paginator->perPage(),
                'has_more_pages' => $paginator->hasMorePages()
            ],
            'message' => 'success',
            'code' => \App\Entities\HttpCode::SUCCESS
        ];
    }

    public static function getCompetitionMatches(array $data)
    {
        $lang = \Illuminate\Support\Facades\App::getLocale();
        $type = isset($data['type']) ? $data['type'] : 'today';
        $todayStr = date('Y-m-d');
        
        if (!isset($data['competition_id'])) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => \App\Entities\HttpCode::ERROR
            ];
        }

        $query = \App\Models\SportMatch::with(['team1Club', 'team2Club'])
            ->where('competition_row_id', $data['competition_id']);

        if ($type === 'previous') {
            $query->where('match_date', '<', $todayStr)->orderBy('match_date', 'desc');
        } elseif ($type === 'week') {
            $today = \Carbon\Carbon::today();
            $startOfWeek = $today->copy()->startOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
            $endOfWeek = $today->copy()->endOfWeek(\Carbon\Carbon::SATURDAY)->format('Y-m-d');
            $query->whereBetween('match_date', [$startOfWeek, $endOfWeek])->orderBy('match_date', 'asc');
        } elseif ($type === 'upcoming' || $type === 'next') {
            $query->where('match_date', '>', $todayStr)->orderBy('match_date', 'asc');
        } else {
            $query->where('match_date', '=', $todayStr)->orderBy('match_date', 'asc');
        }

        $page = isset($data['page']) ? max(1, (int)$data['page']) : 1;
        $pageSize = isset($data['page_size']) ? max(1, (int)$data['page_size']) : 10;
        
        $paginator = $query->paginate($pageSize, ['*'], 'page', $page);
        
        $matchesData = [];
        foreach ($paginator->items() as $match) {
            $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
            $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
            
            if (!$team1Logo) {
                $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                if ($club1 && $club1->logo) $team1Logo = url($club1->logo);
                else {
                    $team1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                    $team1Logo = $team1 && $team1->image ? url($team1->image) : url('images/default-logo.png');
                }
            }
            if (!$team2Logo) {
                $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                if ($club2 && $club2->logo) $team2Logo = url($club2->logo);
                else {
                    $team2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                    $team2Logo = $team2 && $team2->image ? url($team2->image) : url('images/default-logo.png');
                }
            }

            $matchesData[] = [
                'id' => $match->row_id,
                'team1' => $match->team1,
                'team1_logo' => $team1Logo,
                'team2' => $match->team2,
                'team2_logo' => $team2Logo,
                'team1_result' => $match->team1_result,
                'team2_result' => $match->team2_result,
                'match_date' => $match->match_date,
                'match_time' => $match->match_time,
                'stage_round' => $match->stage_round,
                'pitch' => $match->pitch,
                'week' => $match->week,
                'live_link' => $match->live_link,
                'fanet_match_id' => $match->fanet_match_id,
            ];
        }

        return [
            'data' => [
                'matches' => $matchesData,
                'total_matches' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'page_size' => $paginator->perPage(),
                'has_more_pages' => $paginator->hasMorePages()
            ],
            'message' => 'success',
            'code' => \App\Entities\HttpCode::SUCCESS
        ];
    }

    public static function getMatchDetails(array $data)
    {
        $lang = \Illuminate\Support\Facades\App::getLocale();
        
        if (!isset($data['match_id'])) {
            return [
                'message' => trans('api.general_error_message'),
                'code' => \App\Entities\HttpCode::ERROR
            ];
        }

        $match = \App\Models\SportMatch::with(['team1Club', 'team2Club', 'competition.season'])
            ->where('row_id', $data['match_id'])
            ->first();

        if (!$match) {
            return [
                'data' => null,
                'message' => 'success',
                'code' => \App\Entities\HttpCode::SUCCESS
            ];
        }

        $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
        $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
        
        if (!$team1Logo) {
            $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
            if ($club1 && $club1->logo) $team1Logo = url($club1->logo);
            else {
                $team1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                $team1Logo = $team1 && $team1->image ? url($team1->image) : url('images/default-logo.png');
            }
        }
        
        if (!$team2Logo) {
            $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
            if ($club2 && $club2->logo) $team2Logo = url($club2->logo);
            else {
                $team2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                $team2Logo = $team2 && $team2->image ? url($team2->image) : url('images/default-logo.png');
            }
        }
        
        $matchData = [
            'id' => $match->row_id,
            'team1' => $match->team1,
            'team1_logo' => $team1Logo,
            'team2' => $match->team2,
            'team2_logo' => $team2Logo,
            'team1_result' => $match->team1_result,
            'team2_result' => $match->team2_result,
            'match_date' => $match->match_date,
            'match_time' => $match->match_time,
            'stage_round' => $match->stage_round,
            'pitch' => $match->pitch,
            'week' => $match->week,
            'live_link' => $match->live_link,
            'fanet_match_id' => $match->fanet_match_id,
            'competition_name' => $match->competition ? ($lang == 'ar' ? $match->competition->name_ar : $match->competition->name_en) : null,
            'season_name' => ($match->competition && $match->competition->season) ? $match->competition->season->name : null,
        ];

        return [
            'data' => $matchData,
            'message' => 'success',
            'code' => \App\Entities\HttpCode::SUCCESS
        ];
    }

    public static function getNextHeroMatch(array $data)
    {
        $todayStr = date('Y-m-d');
        
        $match = \App\Models\SportMatch::with(['team1Club', 'team2Club', 'competition.season'])
            ->where('match_date', '>=', $todayStr)
            ->orderBy('match_date', 'asc')
            ->orderBy('match_time', 'asc')
            ->first();

        if (!$match) {
            return [
                'data' => null,
                'message' => 'success',
                'code' => \App\Entities\HttpCode::SUCCESS
            ];
        }

        $team1Logo = $match->team1Club && $match->team1Club->logo ? url($match->team1Club->logo) : null;
        $team2Logo = $match->team2Club && $match->team2Club->logo ? url($match->team2Club->logo) : null;
        
        if (!$team1Logo) {
            $club1 = \App\Models\Club::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
            if ($club1 && $club1->logo) $team1Logo = url($club1->logo);
            else {
                $team1 = \App\Models\SportTeam::where('name_ar', $match->team1)->orWhere('name_en', $match->team1)->first();
                $team1Logo = $team1 && $team1->image ? url($team1->image) : url('images/default-logo.png');
            }
        }
        
        if (!$team2Logo) {
            $club2 = \App\Models\Club::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
            if ($club2 && $club2->logo) $team2Logo = url($club2->logo);
            else {
                $team2 = \App\Models\SportTeam::where('name_ar', $match->team2)->orWhere('name_en', $match->team2)->first();
                $team2Logo = $team2 && $team2->image ? url($team2->image) : url('images/default-logo.png');
            }
        }

        $lang = \Illuminate\Support\Facades\App::getLocale();
        
        $matchData = [
            'id' => $match->row_id,
            'team1' => $match->team1,
            'team1_logo' => $team1Logo,
            'team2' => $match->team2,
            'team2_logo' => $team2Logo,
            'match_date' => $match->match_date,
            'match_time' => $match->match_time,
            'stage_round' => $match->stage_round,
            'pitch' => $match->pitch,
            'week' => $match->week,
            'live_link' => $match->live_link,
            'fanet_match_id' => $match->fanet_match_id,
            'competition_name' => $match->competition ? ($lang == 'ar' ? $match->competition->name_ar : $match->competition->name_en) : null,
            'season_name' => ($match->competition && $match->competition->season) ? $match->competition->season->name : null,
        ];

        return [
            'data' => $matchData,
            'message' => 'success',
            'code' => \App\Entities\HttpCode::SUCCESS
        ];
    }
}
