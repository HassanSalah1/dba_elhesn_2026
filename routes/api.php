<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Setting\SettingController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\SqlServerController;

use App\Http\Controllers\Api\V2\Auth\AuthController as AuthV2Controller;
use App\Http\Controllers\Api\V2\Setting\SettingController as SettingV2Controller;
use App\Http\Controllers\Api\V2\User\UserController as UserV2Controller;
use App\Http\Controllers\Api\V2\SqlServerController as SqlServerV2Controller;
use App\Http\Controllers\Api\V2\User\ClinicController as ClinicV2Controller;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'lang'], function () {
    Route::group(['prefix' => 'v1'], function () {

        Route::get('/handle/teams', [SqlServerController::class, 'getTeams']); // get intros
        ////////////////////////////////////////
        ///
        Route::get('/handle/site', [SettingController::class, 'getSiteNews']); // get intros

        Route::get('/intros', [SettingController::class, 'getIntros']); // get intros

        Route::get('/terms', [SettingController::class, 'getTerms']); // get terms
        Route::get('/contact', [SettingController::class, 'getContactDetails']); // get contact details

        Route::post('/contact', [SettingController::class, 'addContact']); // contact us

        Route::get('/teams', [SettingController::class, 'getTeams']); // get teams

        Route::get('/gallery', [SettingController::class, 'getGallery']); // get gallery
        Route::get('/sport/games', [SettingController::class, 'getSportGames']); // get SportGames
        Route::get('/sport/games/{id}/gallery', [SettingController::class, 'getSportGamesGallery']); // get SportGames

        Route::get('/sport/teams/{id}', [SettingController::class, 'getSportTeams']); // get Sport teams
        Route::get('/team/players/{id}', [SettingController::class, 'getTeamPlayers']); // get Sport teams
        Route::get('/team/player/details/{id}', [SettingController::class, 'getTeamPlayerDetails']);

        Route::get('/history', [SettingController::class, 'getHistory']); // history

        Route::get('/categories', [SettingController::class, 'getCategories']); // get categories

        Route::get('/home', [SettingController::class, 'getHome']); // get news

        Route::get('/news', [SettingController::class, 'getNews']); // get news
        Route::get('/new/details/{id}', [SettingController::class, 'getNewDetails']); // get new details

        Route::get('/actions', [SettingController::class, 'getActions']); // get actions
        Route::get('/action/details/{id}', [SettingController::class, 'getActionDetails']); // get action details

        Route::get('/about-dba', [SettingController::class, 'getAbout']); // get about
        Route::get('/committees', [SettingController::class, 'getCommittees']); // get committees

        Route::get('/magles', [SettingController::class, 'getMagles']); // get about
        Route::get('/club_structure', [SettingController::class, 'getClubStructure']); // get about

        Route::get('/elders', [SettingController::class, 'getElders']);
        Route::get('/emergencies', [SettingController::class, 'getEmergencies']);

        Route::post('/signup', [AuthController::class, 'register']); // register new user
        Route::get('/get/verification/code', [AuthController::class, 'getVerificationCode']); // get verification code'Auth\@getVerificationCode'); // get verification code
        Route::post('/verify/check', [AuthController::class, 'checkVerificationCode']); // get verification code'Auth\@checkVerificationCode'); // check verification code
        Route::post('/verify/resend', [AuthController::class, 'resendVerificationCode']); // resend verification code
        Route::post('/login', [AuthController::class, 'login']); // login user
        Route::post('/password/forget', [AuthController::class, 'forgetPassword']); // forget password
        Route::post('/password/forget/change', [AuthController::class, 'changeForgetPassword']); // change forget password


        Route::get('/regulations', [SettingController::class, 'getRegulations']); // get regulations

        Route::post('/image/upload', [UserController::class, 'uploadImage']);
        Route::post('/sport/subscribe', [UserController::class, 'subscribeSport']);

        Route::get('/competitions/{id}', [UserController::class, 'getCompetitions']);

//        Route::get('/get/home', [HomeController::class, 'getHome']); // get Home data

        Route::group(['middleware' => ['auth:api', 'authApi']], function () {

            Route::post('/logout', [AuthController::class, 'logout']); // logout

            Route::get('/profile/get', [UserController::class, 'getProfile']); // get profile
            Route::post('/profile/edit', [UserController::class, 'editProfile']); // edit profile
            Route::post('/delete-account', [AuthController::class, 'deleteAccount']); // delete Account (only deactivate)

            Route::get('/profile/download/card', [UserController::class, 'downloadProfileCard']); // download Profile Card

            // get my notifications
            Route::get('/my/notifications', [UserController::class, 'getMyNotifications']); // get my notifications
            Route::get('/notifications/count', [UserController::class, 'getNotificationsCount']); // get my notifications



            Route::get('/my/teams', [UserController::class, 'getMyTeams']);
            Route::post('/administrative_report', [UserController::class, 'administrativeReport']);
            Route::post('/advance_requests', [UserController::class, 'advanceRequests']);
            Route::get('/my/team/players/{id}', [UserController::class, 'getTeamPlayers']);

            Route::get('/reasons', [UserController::class, 'getReasons']);
            Route::post('/presence_absence', [UserController::class, 'presenceAbsence']);
            Route::post('/general_evaluation', [UserController::class, 'generalEvaluation']);
            Route::post('/coach_evaluation/sports', [UserController::class, 'getSports']);
            Route::post('/coach_evaluation', [UserController::class, 'coachEvaluation']);
            Route::post('/coach_evaluation/seasons', [UserController::class, 'getSeasons']);



            Route::get('/matches', [UserController::class, 'getMatches']);
            Route::post('/match/update', [UserController::class, 'updateMatcheResult']);

        });
    });

    Route::group(['prefix' => 'v2'], function () {

        Route::get('/handle/teams', [SqlServerV2Controller::class, 'getTeams']); // get intros
        ////////////////////////////////////////
        ///
        Route::get('/handle/site', [SettingV2Controller::class, 'getSiteNews']); // get intros

        Route::get('/intros', [SettingV2Controller::class, 'getIntros']); // get intros

        Route::get('/terms', [SettingV2Controller::class, 'getTerms']); // get terms
        Route::get('/contact', [SettingV2Controller::class, 'getContactDetails']); // get contact details

        Route::post('/contact', [SettingV2Controller::class, 'addContact']); // contact us

        Route::get('/teams', [SettingV2Controller::class, 'getTeams']); // get teams

        Route::get('/gallery', [SettingV2Controller::class, 'getGallery']); // get gallery
        Route::get('/sport/games', [SettingV2Controller::class, 'getSportGames']); // get SportGames
        Route::get('/sport/games/{id}/gallery', [SettingV2Controller::class, 'getSportGamesGallery']); // get SportGames

        Route::get('/sport/teams/{id}', [SettingV2Controller::class, 'getSportTeams']); // get Sport teams
        Route::get('/team/players/{id}', [SettingV2Controller::class, 'getTeamPlayers']); // get Sport teams
        Route::get('/team/player/details/{id}', [SettingV2Controller::class, 'getTeamPlayerDetails']);

        Route::get('/history', [SettingV2Controller::class, 'getHistory']); // history

        Route::get('/categories', [SettingV2Controller::class, 'getCategories']); // get categories

        Route::get('/home', [SettingV2Controller::class, 'getHome']); // get news

        Route::get('/news', [SettingV2Controller::class, 'getNews']); // get news
        Route::get('/new/details/{id}', [SettingV2Controller::class, 'getNewDetails']); // get new details

        Route::get('/actions', [SettingV2Controller::class, 'getActions']); // get actions
        Route::get('/action/details/{id}', [SettingV2Controller::class, 'getActionDetails']); // get action details

        Route::get('/about-dba', [SettingV2Controller::class, 'getAbout']); // get about
        Route::get('/committees', [SettingV2Controller::class, 'getCommittees']); // get committees

        Route::get('/magles', [SettingV2Controller::class, 'getMagles']); // get about
        Route::get('/club_structure', [SettingV2Controller::class, 'getClubStructure']); // get about

        Route::get('/elders', [SettingV2Controller::class, 'getElders']);
        Route::get('/emergencies', [SettingV2Controller::class, 'getEmergencies']);

        Route::post('/signup', [AuthV2Controller::class, 'register']); // register new user
        Route::get('/get/verification/code', [AuthV2Controller::class, 'getVerificationCode']); // get verification code
        Route::post('/verify/check', [AuthV2Controller::class, 'checkVerificationCode']); // check verification code
        Route::post('/verify/resend', [AuthV2Controller::class, 'resendVerificationCode']); // resend verification code
        Route::post('/login', [AuthV2Controller::class, 'login']); // login user
        Route::post('/password/forget', [AuthV2Controller::class, 'forgetPassword']); // forget password
        Route::post('/password/forget/change', [AuthV2Controller::class, 'changeForgetPassword']); // change forget password


        Route::get('/regulations', [SettingV2Controller::class, 'getRegulations']); // get regulations

        Route::post('/image/upload', [UserV2Controller::class, 'uploadImage']);
        Route::post('/sport/subscribe', [UserV2Controller::class, 'subscribeSport']);

        Route::get('/competitions/{id}', [UserV2Controller::class, 'getCompetitions']);

        Route::group(['middleware' => ['auth:api', 'authApi']], function () {

            Route::post('/logout', [AuthV2Controller::class, 'logout']); // logout

            Route::get('/profile/get', [UserV2Controller::class, 'getProfile']); // get profile
            Route::post('/profile/edit', [UserV2Controller::class, 'editProfile']); // edit profile
            Route::post('/delete-account', [AuthV2Controller::class, 'deleteAccount']); // delete Account (only deactivate)

            Route::get('/profile/download/card', [UserV2Controller::class, 'downloadProfileCard']); // download Profile Card

            // get my notifications
            Route::get('/my/notifications', [UserV2Controller::class, 'getMyNotifications']); // get my notifications
            Route::get('/notifications/count', [UserV2Controller::class, 'getNotificationsCount']); // get my notifications



            Route::get('/my/teams', [UserV2Controller::class, 'getMyTeams']);
            Route::post('/administrative_report', [UserV2Controller::class, 'administrativeReport']);
            Route::post('/advance_requests', [UserV2Controller::class, 'advanceRequests']);
            Route::get('/my/team/players/{id}', [UserV2Controller::class, 'getTeamPlayers']);

            Route::get('/reasons', [UserV2Controller::class, 'getReasons']);
            Route::post('/presence_absence', [UserV2Controller::class, 'presenceAbsence']);
            Route::post('/general_evaluation', [UserV2Controller::class, 'generalEvaluation']);
            Route::post('/coach_evaluation/sports', [UserV2Controller::class, 'getSports']);
            Route::post('/coach_evaluation', [UserV2Controller::class, 'coachEvaluation']);
            Route::post('/coach_evaluation/seasons', [UserV2Controller::class, 'getSeasons']);



            Route::get('/matches', [UserV2Controller::class, 'getMatches']);
            Route::post('/match/update', [UserV2Controller::class, 'updateMatcheResult']);

            // Clinic Bookings
            Route::get('/clinic/time-slots', [ClinicV2Controller::class, 'getTimeSlots']);
            Route::post('/clinic/booking', [ClinicV2Controller::class, 'createBooking']);
            Route::get('/clinic/bookings', [ClinicV2Controller::class, 'getBookings']);
            Route::get('/clinic/booking/{id}', [ClinicV2Controller::class, 'getBookingDetails']);
            Route::post('/clinic/booking/{id}/cancel', [ClinicV2Controller::class, 'cancelBooking']);
            Route::post('/clinic/booking/{id}/attachment', [ClinicV2Controller::class, 'addAttachment']);
            Route::delete('/clinic/booking/{id}/attachment/{attachmentId}', [ClinicV2Controller::class, 'deleteAttachment']);
        });
    });
});


