<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Setting\SettingController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\SqlServerController;

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

        Route::get('/history', [SettingController::class, 'getHistory']); // history

        Route::get('/categories', [SettingController::class, 'getCategories']); // get categories

        Route::get('/home', [SettingController::class, 'getHome']); // get news

        Route::get('/news', [SettingController::class, 'getNews']); // get news
        Route::get('/new/details/{id}', [SettingController::class, 'getNewDetails']); // get new details

        Route::get('/actions', [SettingController::class, 'getActions']); // get actions
        Route::get('/action/details/{id}', [SettingController::class, 'getActionDetails']); // get action details

        Route::get('/about-dba', [SettingController::class, 'getAbout']); // get about
        Route::get('/committees', [SettingController::class, 'getCommittees']); // get committees


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
        });
    });
});
