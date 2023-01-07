<?php
namespace App\Repositories\Dashboard\Employee;


use App\Entities\Status;
use App\Entities\UserRoles;
use App\Models\User;
use App\Repositories\General\UtilsRepository;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class EmployeeRepository
{

    // get Employees and create datatable data.
    public static function getEmployeesData(array $data)
    {
        $employees = User::where([
            'role' => UserRoles::EMPLOYEE,
            ['id' , '!=' , auth()->id()]
        ])->orderBy('id', 'DESC');
        return DataTables::of($employees)
            ->addColumn('group_name', function ($employee) {
                return @$employee->group->group_name;
            })
            ->addColumn('actions', function ($employee) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $employee->id . '" onclick="editEmployee(this);return false;" href="#" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $employee->id . '" onclick="deleteEmployee(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addEmployee(array $data)
    {
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_name = 'image';
        $image_path = 'uploads/admins/';
        $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id);


        $employeeData = [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?: null,
            'password' => Hash::make($data['password']),
            'role' => UserRoles::EMPLOYEE,
            'status' => Status::ACTIVE,
            'image' => $image !== false ? $image : null,
            'group_id' => $data['group_id']
        ];
        $created = User::create($employeeData);
        if ($created) {
            return true;
        }
        return false;
    }

    public static function deleteEmployee(array $data)
    {
        $employee = User::where(['id' => $data['id']])->first();
        if ($employee) {
            $employee->delete();
            return true;
        }
        return false;
    }

    public static function restoreEmployee(array $data)
    {
        $bank = User::where(['id' => $data['id']])->first();
        if ($bank) {
            $bank->restore();
            return true;
        }
        return false;
    }

    public static function getEmployeeData(array $data)
    {
        $employee = User::where(['id' => $data['id']])->first();
        if ($employee) {
            return $employee;
        }
        return false;
    }

    public static function editEmployee(array $data)
    {
        $employee = User::where(['id' => $data['id']])->first();
        if ($employee) {
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/admins/';
            $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id);
            if ($image !== false) {
                if ($employee->image && file_exists($employee->image)) {
                    unlink($employee->image);
                }
            } else {
                $image = $employee->image;
            }
            $employeeData = [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?: $employee->email,
                'image' => $image !== false ? $image : null,
                'group_id' => $data['group_id']
            ];

            if (isset($data['password']) && !empty($data['password'])) {
                $employeeData['password'] = Hash::make($data['password']);
            }

            $updated = $employee->update($employeeData);
            if ($updated) {
                return true;
            }
        }
        return false;
    }

}

?>
