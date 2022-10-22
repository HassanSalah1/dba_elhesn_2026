<?php

namespace App\Http\Controllers\Dashboard\Setting;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Setting\RegulationService;
use Illuminate\Http\Request;

class RegulationController extends Controller
{
    //
    public function showRegulations()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.regulations_title');
        $data['debatable_names'] = array(trans('admin.name'), trans('admin.description'),
            trans('admin.actions'));
        return view('admin.settings.regulations')->with($data);
    }

    public function getRegulationsData(Request $request)
    {
        $data = $request->all();
        return RegulationService::getRegulationsData($data);
    }

    public function addRegulation(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return RegulationService::addRegulation($data);
    }

    public function deleteRegulation(Request $request)
    {
        $data = $request->all();
        return RegulationService::deleteRegulation($data);
    }

    public function restoreRegulation(Request $request)
    {
        $data = $request->all();
        return RegulationService::restoreRegulation($data);
    }

    public function getRegulationData(Request $request)
    {
        $data = $request->all();
        return RegulationService::getRegulationData($data);
    }

    public function editRegulation(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return RegulationService::editRegulation($data);
    }

}
