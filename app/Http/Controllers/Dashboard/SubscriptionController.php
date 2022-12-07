<?php

namespace App\Http\Controllers\Dashboard;


use App\Http\Controllers\Controller;
use App\Services\Dashboard\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function showSubscriptions(Request $request)
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.subscription_title');
        $data['debatable_names'] = array(trans('admin.player_full_name_ar'),
            trans('admin.player_full_name_en'),trans('admin.parent_full_name_en'),
            trans('admin.parent_full_name_en'), trans('admin.nationality'), trans('admin.actions'));
        return view('admin.subscription.index')->with($data);
    }

    public function getSubscriptionsData(Request $request)
    {
        $data = $request->all();
        return SubscriptionService::getSubscriptionsData($data);
    }

}
