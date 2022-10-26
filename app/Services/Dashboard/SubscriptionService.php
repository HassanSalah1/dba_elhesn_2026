<?php
namespace App\Services\Dashboard;

use App\Repositories\Dashboard\Setting\CommitteeRepository;
use App\Repositories\Dashboard\SubscriptionRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class SubscriptionService
{


    public static function getSubscriptionsData(array $data)
    {
        return SubscriptionRepository::getSubscriptionsData($data);
    }
}
