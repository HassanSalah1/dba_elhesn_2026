<?php

namespace App\Repositories\Dashboard;

use App\Models\Subscribe;
use Yajra\DataTables\Facades\DataTables;

class SubscriptionRepository
{

    public static function getSubscriptionsData(array $data)
    {
        $subscriptions = Subscribe::orderBy('id', 'DESC');
        return DataTables::of($subscriptions)
            ->addColumn('sport_name', function ($subscription) {
                return $subscription->sport->title;
            })
            ->addColumn('actions', function ($subscription) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.show') . '" id="' . $subscription->id . '" href="' . url('/admin/sport/subscription/show/' . $subscription->id) . '" class="on-default edit-row btn btn-info"><i data-feather="eye"></i></a>
                   ';
                return $ul;
            })->make(true);
    }
}
