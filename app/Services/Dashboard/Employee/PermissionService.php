<?php
namespace App\Services\Dashboard\Employee;

use App\Models\Permission;
use App\Repositories\Dashboard\Employee\PermissionRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class PermissionService
{


    public static function getPermissionsData(array $data)
    {
        return PermissionRepository::getPermissionsData($data);
    }

    public static function addPermission(array $data)
    {
        $rules = [
            'group_name' => 'required|unique:permissions',
            'permissions' => 'required'
        ];
        $messages = [
            'unique' => trans('admin.group_name_unique_message'),
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = PermissionRepository::addPermission($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , trans('admin.success_title'));
    }

    public static function deletePermission(array $data)
    {
        $response = PermissionRepository::deletePermission($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , trans('admin.success_title'));
    }

    public static function getPermissionData(array $data)
    {
        $response = PermissionRepository::getPermissionData($data);
        return UtilsRepository::response($response);
    }

    public static function editPermission(array $data)
    {
        $permission = Permission::where(['id' => $data['id']])->first();
        $rules = [
            'group_name' => 'required',
            'permissions' => 'required'
        ];
        $messages = [
            'unique' => trans('admin.group_name_unique_message'),
        ];
        if($permission->group_name !== $data['group_name']){
            $rules['group_name'] .= '|unique:permissions';
        }
        $validated = ValidationRepository::validateWebGeneral($data, $rules , $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = PermissionRepository::editPermission($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , trans('admin.success_title'));
    }
}

?>
