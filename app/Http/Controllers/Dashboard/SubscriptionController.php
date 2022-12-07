<?php

namespace App\Http\Controllers\Dashboard;


use App\Http\Controllers\Controller;
use App\Models\Subscribe;
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
            trans('admin.parent_full_name_ar'), trans('admin.parent_phone'),
            trans('admin.parent_email'), trans('admin.nationality'),
            trans('admin.sport_name'), trans('admin.actions'));
        return view('admin.subscription.index')->with($data);
    }

    public function getSubscriptionsData(Request $request)
    {
        $data = $request->all();
        return SubscriptionService::getSubscriptionsData($data);
    }


    public function showSubscription(Request $request, $id)
    {
        $data = $request->all();
        $data['subscribeObj'] = Subscribe::find($id);
        return view('admin.subscription.show')->with($data);
    }

}
