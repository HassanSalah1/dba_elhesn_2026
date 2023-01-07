<?php

namespace App\Http\Controllers\Dashboard\Employee;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Employee\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PermissionController extends Controller
{

    public function showPermissions()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.permissions_title');
        $data['debatable_names'] = array(trans('admin.group_name'), trans('admin.permissions'),
            trans('admin.actions'));
        return view('admin.permissions.index')->with($data);
    }

    public function getPermissionsData(Request $request)
    {
        $data = $request->all();
        $data['locale'] = App::getLocale();
        return PermissionService::getPermissionsData($data);
    }

    public function addPermission(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return PermissionService::addPermission($data);
    }

    public function deletePermission(Request $request)
    {
        $data = $request->all();
        return PermissionService::deletePermission($data);
    }

    public function getPermissionData(Request $request)
    {
        $data = $request->all();
        return PermissionService::getPermissionData($data);
    }

    public function editPermission(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return PermissionService::editPermission($data);
    }

}

?>
