<?php
namespace App\Services\Dashboard\Employee;

use App\Repositories\Dashboard\Employee\EmployeeRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class EmployeeService
{


    public static function getEmployeesData(array $data)
    {
        return EmployeeRepository::getEmployeesData($data);
    }

    public static function addEmployee(array $data)
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required|unique:users|phone:SA,mobile|max:9',
            'password' => 'required',
            'image' => 'mimes:jpg,jpeg',
            'group_id' => 'required',
        ];
        $messages = [
            'unique' => trans('admin.phone_unique_message'),
            'phone' => trans('api.valid_phone_error_message'),
            'max' => trans('api.valid_phone_error_message')
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = EmployeeRepository::addEmployee($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function deleteEmployee(array $data)
    {
        $response = EmployeeRepository::deleteEmployee($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function restoreEmployee(array $data)
    {
        $response = EmployeeRepository::restoreEmployee($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }

    public static function getEmployeeData(array $data)
    {
        $response = EmployeeRepository::getEmployeeData($data);
        return UtilsRepository::response($response);
    }

    public static function editEmployee(array $data)
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required|phone:SA,mobile|max:9',
            'image' => 'mimes:jpg,jpeg',
            'group_id' => 'required',
        ];
        $messages = [
            'unique' => trans('admin.phone_unique_message'),
            'phone' => trans('api.valid_phone_error_message'),
            'max' => trans('api.valid_phone_error_message')
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules, $messages);
        if ($validated !== true) {
            return $validated;
        }
        $response = EmployeeRepository::editEmployee($data);
        return UtilsRepository::response($response, trans('admin.process_success_message')
            , '');
    }
}

?>
