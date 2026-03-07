<?php

use App\Http\Controllers\Dashboard\Action\ActionController;
use App\Http\Controllers\Dashboard\Auth\AuthenticationController;
use App\Http\Controllers\Dashboard\Contact\ContactController;
use App\Http\Controllers\Dashboard\Employee\EmployeeController;
use App\Http\Controllers\Dashboard\Employee\PermissionController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\News\CategoryController;
use App\Http\Controllers\Dashboard\News\NewController;
use App\Http\Controllers\Dashboard\Notification\NotificationController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\Setting\CommitteeController;
use App\Http\Controllers\Dashboard\Setting\GalleryController;
use App\Http\Controllers\Dashboard\Setting\IntroController;
use App\Http\Controllers\Dashboard\Setting\RegulationController;
use App\Http\Controllers\Dashboard\Setting\SportGameController;
use App\Http\Controllers\Dashboard\Setting\TeamController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\User\UserController;


//Route::get('/', function () {
//    echo \Illuminate\Support\Facades\Hash::make('s@*Rv31E4Kn0avi');
//});

Route::group(['prefix' => 'admin', 'middleware' => 'web'], function () {
    Route::get('card', [AuthenticationController::class, 'showCard']);

    Route::group(['prefix' => 'auth'], function () {
        Route::get('login', [AuthenticationController::class, 'showLogin']);
        Route::post('login', [AuthenticationController::class, 'processLogin']);
    });

    Route::middleware(["admin", "web"])->group(function () {
        Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout'); // logout current user

        Route::get('/home', [HomeController::class, 'showHome'])->name('dashboard-home'); // show home page


        ////////////////////////////////////////////////////////////
        Route::get('/permissions', [PermissionController::class, 'showPermissions'])->name('dashboard-permissions'); // show Index page that control all permissions
        Route::get('/permissions/data', [PermissionController::class, 'getPermissionsData']); // get all Permissions data for DataTable
        Route::post('/permission/add', [PermissionController::class, 'addPermission']); // add Permission
        Route::post('/permission/data', [PermissionController::class, 'getPermissionData']); // get Permission data
        Route::post('/permission/edit', [PermissionController::class, 'editPermission']); // edit Permission
        Route::post('/permission/delete', [PermissionController::class, 'deletePermission']); // delete Permission

        ////////////////////////////////////////////////////////////

        Route::get('/employees', [EmployeeController::class, 'showEmployees'])->name('dashboard-employees'); // show Index page that control all employees
        Route::get('/employees/data', [EmployeeController::class, 'getEmployeesData']); // get all Employees data for DataTable
        Route::post('/employee/add', [EmployeeController::class, 'addEmployee']); // add Employee
        Route::post('/employee/data', [EmployeeController::class, 'getEmployeeData']); // get Employee data
        Route::post('/employee/edit', [EmployeeController::class, 'editEmployee']); // edit Employee
        Route::post('/employee/delete', [EmployeeController::class, 'deleteEmployee']); // delete Employee


        ////////////////////////////////////////////////////////////
        Route::get('/intros', [IntroController::class, 'showIntros'])->name('dashboard-intros'); // show Index page that control all Intros
        Route::get('/intros/data', [IntroController::class, 'getIntrosData']); // get all Intros data for DataTable
        Route::post('/intro/add', [IntroController::class, 'addIntro']); // add Intro
        Route::post('/intro/data', [IntroController::class, 'getIntroData']); // get Intro data
        Route::post('/intro/edit', [IntroController::class, 'editIntro']); // edit Intro
        Route::post('/intro/delete', [IntroController::class, 'deleteIntro']); // delete Intro
        ////////////////////////////////
        Route::get('/terms', [HomeController::class, 'showTerms'])->name('dashboard-terms'); // about page
        Route::post('/terms/save', [HomeController::class, 'saveTerms']);

        ////////////////////////////////
        Route::get('/magles', [HomeController::class, 'showMagles'])->name('dashboard-magles'); // about page
        Route::post('/magles/save', [HomeController::class, 'saveMagles']);
        ////////////////////////////////
        Route::post('/upload/image', [HomeController::class, 'uploadEditorImages']); // upload editor images inside text
        //////////////////////////////////
        Route::get('/setting', [HomeController::class, 'showSetting'])->name('dashboard-setting'); // about page
        Route::post('/setting/save', [HomeController::class, 'saveSetting']);

        //////////////////////////////////
        Route::get('/elders', [HomeController::class, 'showElders'])->name('dashboard-elders'); // about page
        Route::post('/elders/save', [HomeController::class, 'saveElders']);

        ////////////////////////////////
        Route::get('/contacts', [ContactController::class, 'showContacts'])->name('dashboard-contacts'); // show Index page that control all Contacts
        Route::get('/contacts/data', [ContactController::class, 'getContactsData']); // get all Contacts data for DataTable
        Route::post('/contact/replay', [ContactController::class, 'replayContact']); // replay Contact

        ////////////////////////////////
        Route::get('/users', [UserController::class, 'showUsers'])->name('dashboard-users'); // show Index page that control all users
        Route::get('/users/data', [UserController::class, 'getUsersData']); // get all users data for DataTable

        Route::get('/user/details/{id}', [UserController::class, 'showUserDetails']); // show user details
        Route::post('/user/verify', [UserController::class, 'verifyUser']); // verify user
        Route::post('/user/change', [UserController::class, 'changeStatus']); // change user Status


        ////////////////////////////////
        Route::get('/teams', [TeamController::class, 'showTeams'])->name('dashboard-testimonials'); // show Index page that control all Teams
        Route::get('/teams/data', [TeamController::class, 'getTeamsData']); // get all Teams data for DataTable
        Route::post('/team/add', [TeamController::class, 'addTeam']); // add Team
        Route::post('/team/data', [TeamController::class, 'getTeamData']); // get Team data
        Route::post('/team/edit', [TeamController::class, 'editTeam']); // edit Team
        Route::post('/team/delete', [TeamController::class, 'deleteTeam']); // delete Team

        ////////////////////////////////
        Route::get('/galleries', [GalleryController::class, 'showGalleries'])->name('dashboard-galleries'); // show Index page that control all Galleries
        Route::get('/sport/galleries/{id}', [GalleryController::class, 'showGalleries']); // show Index page that control all Galleries
        Route::get('/galleries/data', [GalleryController::class, 'getGalleriesData']); // get all Galleries data for DataTable
        Route::post('/gallery/add', [GalleryController::class, 'addGallery']); // add Gallery
        Route::post('/gallery/data', [GalleryController::class, 'getGalleryData']); // get Gallery data
        Route::post('/gallery/edit', [GalleryController::class, 'editGallery']); // edit Gallery
        Route::post('/gallery/delete', [GalleryController::class, 'deleteGallery']); // delete Gallery

        ////////////////////////////////
        Route::get('/committees', [CommitteeController::class, 'showCommittees'])->name('dashboard-committee'); // show Index page that control all Committees
        Route::get('/committees/data', [CommitteeController::class, 'getCommitteesData']); // get all Committees data for DataTable
        Route::post('/committee/add', [CommitteeController::class, 'addCommittee']); // add Committee
        Route::post('/committee/data', [CommitteeController::class, 'getCommitteeData']); // get Committee data
        Route::post('/committee/edit', [CommitteeController::class, 'editCommittee']); // edit Committee
        Route::post('/committee/delete', [CommitteeController::class, 'deleteCommittee']); // delete Committee


        ////////////////////////////////
        Route::get('/regulations', [RegulationController::class, 'showRegulations'])->name('dashboard-regulation'); // show Index page that control all Regulations
        Route::get('/regulations/data', [RegulationController::class, 'getRegulationsData']); // get all Regulations data for DataTable
        Route::post('/regulation/add', [RegulationController::class, 'addRegulation']); // add Regulation
        Route::post('/regulation/data', [RegulationController::class, 'getRegulationData']); // get Regulation data
        Route::post('/regulation/edit', [RegulationController::class, 'editRegulation']); // edit Regulation
        Route::post('/regulation/delete', [RegulationController::class, 'deleteRegulation']); // delete Regulation


        ////////////////////////////////
        Route::get('/sportGames', [SportGameController::class, 'showSportGames'])->name('dashboard-sportGames'); // show Index page that control all SportGamess
        Route::get('/sportGames/data', [SportGameController::class, 'getSportGamesData']); // get all SportGamess data for DataTable
        Route::post('/sportGame/add', [SportGameController::class, 'addSportGame']); // add SportGames
        Route::post('/sportGame/data', [SportGameController::class, 'getSportGameData']); // get SportGames data
        Route::post('/sportGame/edit', [SportGameController::class, 'editSportGame']); // edit SportGames
        Route::post('/sportGame/delete', [SportGameController::class, 'deleteSportGame']); // delete SportGames


        ////////////////////////////////
        Route::get('/sport/subscription', [SubscriptionController::class, 'showSubscriptions'])->name('dashboard-subscription'); // show Index page that control all SportGamess
        Route::get('/sport/subscription/data', [SubscriptionController::class, 'getSubscriptionsData']); // get all SportGamess data for DataTable
        Route::get('/sport/subscription/show/{id}', [SubscriptionController::class, 'showSubscription']); // get all SportGamess data for DataTable


        //////////////////////////////////
        Route::get('/history', [HomeController::class, 'showHistory'])->name('dashboard-history'); // history page
        Route::post('/history/save', [HomeController::class, 'saveHistory']);
        //////////////////////////////////
        Route::get('/about', [HomeController::class, 'showAbout'])->name('dashboard-about'); // about page
        Route::post('/about/save', [HomeController::class, 'saveAbout']);


        /////////////////////////////////////////////////
        Route::get('/actions', [ActionController::class, 'showActions'])->name('dashboard-actions'); // show Index page that control all actions
        Route::get('/actions/data', [ActionController::class, 'getActionsData']); // get all actions data for DataTable
        Route::get('/action/add', [ActionController::class, 'showAddAction']); // show add action
        Route::post('/action/add', [ActionController::class, 'addAction']); // add action
        Route::get('/action/edit/{id}', [ActionController::class, 'showEditAction']); // show edit action
        Route::post('/action/edit/{id}', [ActionController::class, 'editAction']); // edit action
        Route::post('/action/delete', [ActionController::class, 'deleteAction']); // delete action
        Route::post('/action/remove_image', [ActionController::class, 'removeImage']); // remove Image

        ////////////////////////////////
        Route::get('/categories', [CategoryController::class, 'showCategories'])->name('dashboard-categories'); // show Index page that control all Categories
        Route::get('/categories/data', [CategoryController::class, 'getCategoriesData']); // get all Categories data for DataTable
        Route::post('/category/add', [CategoryController::class, 'addCategory']); // add Category
        Route::post('/category/data', [CategoryController::class, 'getCategoryData']); // get Category data
        Route::post('/category/edit', [CategoryController::class, 'editCategory']); // edit Category
        Route::post('/category/delete', [CategoryController::class, 'deleteCategory']); // delete Category
        Route::post('/category/restore', [CategoryController::class, 'restoreCategory']); // delete Category

        /////////////////////////////////////////////////
        Route::get('/news', [NewController::class, 'showNews'])->name('dashboard-news'); // show Index page that control all news
        Route::get('/news/data', [NewController::class, 'getNewsData']); // get all news data for DataTable
        Route::get('/new/add', [NewController::class, 'showAddNew']); // show add new
        Route::post('/new/add', [NewController::class, 'addNew']); // add new
        Route::get('/new/edit/{id}', [NewController::class, 'showEditNew']); // show edit new
        Route::post('/new/edit/{id}', [NewController::class, 'editNew']); // edit new
        Route::post('/new/delete', [NewController::class, 'deleteNew']); // delete new
        Route::post('/new/remove_image', [NewController::class, 'removeImage']); // remove Image


        ////////////////////////////////
        Route::get('/notification', [NotificationController::class, 'showSendNotification'])->name('dashboard-show_send_notification'); // show send notification page
        Route::post('/notification/send', [NotificationController::class, 'sendNotification']); // send Notification


        ////////////////////////////////
        Route::get('/administrative_report', [ReportController::class, 'showAdministrativeReport'])->name('dashboard-administrative_report'); // show Index page that control all Teams
        Route::get('/administrative_report/data', [ReportController::class, 'getAdministrativeReportData']); // get all Teams data for DataTable
        ////////////////////////////////
        Route::get('/advance_requests', [ReportController::class, 'showAdvanceRequests'])->name('dashboard-advance_requests'); // show Index page that control all Teams
        Route::get('/advance_requests/data', [ReportController::class, 'getAdvanceRequestsData']); // get all Teams data for DataTable
        ////////////////////////////////
        Route::get('/presence_absence', [ReportController::class, 'showPresenceAbsence'])->name('dashboard-presence_absence'); // show Index page that control all Teams
        Route::get('/presence_absence/data', [ReportController::class, 'getPresenceAbsenceData']); // get all Teams data for DataTable

    });
});

/****************
 * {
 * "name": "reports_title",
 * "icon": "activity",
 * "slug": "",
 * "submenu": [
 * {
 * "name": "administrative_report_title",
 * "url": "/admin/administrative_report",
 * "icon": "circle",
 * "slug": "dashboard-administrative_report"
 * },
 * {
 * "name": "advance_requests_title",
 * "url": "admin/advance_requests",
 * "icon": "circle",
 * "slug": "dashboard-advance_requests"
 * },
 * {
 * "name": "presence_absence_title",
 * "url": "admin/presence_absence",
 * "icon": "circle",
 * "slug": "dashboard-presence_absence"
 * }
 * ],
 * "permission": "reports"
 * },
 ******************/
?>
