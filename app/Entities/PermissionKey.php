<?php

namespace App\Entities;

use App\Interfaces\Enum;

class PermissionKey extends Enum
{
    const PERMISSIONS = 'permissions';
    const EMPLOYEES = 'employees';
    const SETTINGS = 'settings';
    const USERS = 'users';
    const TEAMS = 'teams';
    const COMMITTEES = 'committees';
    const HISTORY = 'history';
    const ABOUT = 'about';
    const TERMS = 'terms';
    const NEWS = 'news';
    const ACTIONS = 'actions';
    const CONTACTS = 'contacts';
    const SPORT_GAMES = 'sport_games';
    const SUBSCRIPTION = 'subscription';
    const REGULATIONS = 'regulations';
    const NOTIFICATION = 'notification';
    const GALLERIES = 'galleries';
}

?>
