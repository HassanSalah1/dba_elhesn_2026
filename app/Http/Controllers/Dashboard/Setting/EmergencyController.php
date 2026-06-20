<?php

namespace App\Http\Controllers\Dashboard\Setting;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Setting\EmergencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EmergencyController extends Controller
{
    public function showEmergencies()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.emergencies_title');
        $data['debatable_names'] = [
            trans('admin.name_ar'),
            trans('admin.name_en'),
            trans('admin.phone'),
            trans('admin.phonecode'),
            trans('admin.order'),
            trans('admin.actions')
        ];
        return view('admin.settings.emergencies')->with($data);
    }

    public function getEmergenciesData(Request $request)
    {
        $data = $request->all();
        $data['locale'] = App::getLocale();
        return EmergencyService::getEmergenciesData($data);
    }

    public function addEmergency(Request $request)
    {
        $data = $request->all();
        return EmergencyService::addEmergency($data);
    }

    public function getEmergencyData(Request $request)
    {
        $data = $request->all();
        return EmergencyService::getEmergencyData($data);
    }

    public function editEmergency(Request $request)
    {
        $data = $request->all();
        return EmergencyService::editEmergency($data);
    }

    public function deleteEmergency(Request $request)
    {
        $data = $request->all();
        return EmergencyService::deleteEmergency($data);
    }
}
