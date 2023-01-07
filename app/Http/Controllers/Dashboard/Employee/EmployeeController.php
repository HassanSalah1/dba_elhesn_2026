<?php

namespace App\Http\Controllers\Dashboard\Employee;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\Dashboard\Employee\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class EmployeeController extends Controller
{

    public function showEmployees()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.employees_title');
        $data['debatable_names'] = array(trans('admin.employee_name'), trans('admin.phone'),
            trans('admin.email'), trans('admin.group_name'), trans('admin.actions'));

        $data['permissionGroups'] = Permission::all();

        return view('admin.employees.index')->with($data);
    }

    public function getEmployeesData(Request $request)
    {
        $data = $request->all();
        $data['locale'] = App::getLocale();
        return EmployeeService::getEmployeesData($data);
    }

    public function addEmployee(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return EmployeeService::addEmployee($data);
    }

    public function deleteEmployee(Request $request)
    {
        $data = $request->all();
        return EmployeeService::deleteEmployee($data);
    }

    public function getEmployeeData(Request $request)
    {
        $data = $request->all();
        return EmployeeService::getEmployeeData($data);
    }


    public function editEmployee(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return EmployeeService::editEmployee($data);
    }

}

?>
