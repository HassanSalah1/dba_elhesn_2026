<?php

namespace App\Http\Controllers\Dashboard;

use App\Entities\BankTransferStatus;
use App\Entities\Key;
use App\Entities\OrderStatus;
use App\Entities\OrderType;
use App\Entities\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\BankTransfer;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Dashboard\HomeRepository;
use App\Repositories\General\UtilsRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    // Dashboard home
    public function showHome()
    {
        $pageConfigs = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $title = trans('admin.home_title');
        $usersCount = User::where(['role' => UserRoles::FAN])->count();


        return view('admin.home', [
            'pageConfigs' => $pageConfigs,
            'title' => $title,
            'usersCount' => $usersCount,
        ]);
    }

    // upload images for editor
    public function uploadEditorImages(Request $request)
    {
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_name = 'file';
        $image_path = 'uploads/content/';
        $image = UtilsRepository::createImage($request, $image_name, $image_path, $file_id);
        if ($image !== false) {
            return response()->json([
                'location' => url($image)
            ]);
        }
    }


    // show terms page
    public function showTerms()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['terms_ar'] = Setting::where(['key' => Key::TERMS_AR])->first();
        $data['terms_en'] = Setting::where(['key' => Key::TERMS_EN])->first();
        $data['title'] = trans('admin.terms_title');
        return view('admin.settings.terms')->with($data);
    }

    // save terms POST request
    public function saveTerms(Request $request)
    {
        $data = $request->all();
        return HomeRepository::saveTerms($data);
    }


// show terms page
    public function showMagles()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['magles_ar'] = Setting::where(['key' => Key::MAGLES_AR])->first();
        $data['magles_en'] = Setting::where(['key' => Key::MAGLES_EN])->first();
        $data['title'] = trans('admin.magles_title');
        return view('admin.settings.magles')->with($data);
    }

    // save terms POST request
    public function saveMagles(Request $request)
    {
        $data = $request->all();
        return HomeRepository::saveMagles($data);
    }

    // show setting page
    public function showSetting()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['email'] = Setting::where(['key' => Key::EMAIL])->first();
        $data['facebook'] = Setting::where(['key' => Key::FACEBOOK])->first();
        $data['twitter'] = Setting::where(['key' => Key::TWITTER])->first();
        $data['instagram'] = Setting::where(['key' => Key::INSTAGRAM])->first();
        $data['youtube'] = Setting::where(['key' => Key::YOUTUBE])->first();
        $data['phone'] = Setting::where(['key' => Key::PHONE])->first();
        $data['latitude'] = Setting::where(['key' => Key::LATITUDE])->first();
        $data['longitude'] = Setting::where(['key' => Key::LONGITUDE])->first();

        $data['CLUB_STRUCTURE'] = Setting::where(['key' => Key::CLUB_STRUCTURE])->first();
        $data['title'] = trans('admin.settings_title');
        return view('admin.settings.setting')->with($data);
    }


    // save setting POST request
    public function saveSetting(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return HomeRepository::saveSetting($data);
    }


    // show about page
    public function showAbout()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['about_ar'] = Setting::where(['key' => Key::CITY_DESCRIPTION_AR])->first();
        $data['about_en'] = Setting::where(['key' => Key::CITY_DESCRIPTION_EN])->first();
        $data['title'] = trans('admin.about_title');
        return view('admin.settings.about')->with($data);
    }

    // save about POST request
    public function saveAbout(Request $request)
    {
        $data = $request->all();
        return HomeRepository::saveAbout($data);
    }

    // show History page
    public function showHistory()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['history_ar'] = Setting::where(['key' => Key::CLUB_HISTORY_AR])->first();
        $data['history_en'] = Setting::where(['key' => Key::CLUB_HISTORY_EN])->first();
        $data['title'] = trans('admin.history_title');
        return view('admin.settings.history')->with($data);
    }

    // save History POST request
    public function saveHistory(Request $request)
    {
        $data = $request->all();
        return HomeRepository::saveHistory($data);
    }

}
