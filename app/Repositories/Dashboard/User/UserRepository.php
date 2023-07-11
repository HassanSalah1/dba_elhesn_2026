<?php
namespace App\Repositories\Dashboard\User;


use App\Entities\IsActive;
use App\Entities\OrderStatus;
use App\Entities\Status;
use App\Entities\UserRoles;
use App\Models\User;
use App\Models\User\Order;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UserRepository
{

    // get Users and create datatable data.
    public static function getUsersData(array $data)
    {
        $users = User::whereIn('role', [UserRoles::FAN, UserRoles::COACH, UserRoles::CoachGK, UserRoles::CoachGKJunior, UserRoles::OFFICIAL , UserRoles::Foot])
            ->select([
                'id',
                'name',
                'email',
                'status',
                'created_at',
                'phone'
            ])->orderBy('id', 'DESC');
        return DataTables::of($users)
            ->addColumn('registration_date', function ($user) {
                return $user->created_at->format('Y-m-d h:i a');
            })
            ->addColumn('actions', function ($user) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.user_details_title') . '" id="' . $user->id . '" href="' . url('/admin/user/details/' . $user->id) . '" class="on-default remove-row btn btn-info"><i data-feather="eye"></i></a> ';
                if ($user->status === Status::INACTIVE) {
                    $ul .= '<a data-toggle="tooltip" title="' . trans('admin.activate_action') . '" id="' . $user->id . '" onclick="activeUser(this);return false;" href="#" class="on-default edit-row btn btn-success"><i data-feather="check"></i></a>
                   ';
                } else if ($user->status === Status::ACTIVE) {
                    $ul .= '<a data-toggle="tooltip" title="' . trans('admin.block_action') . '" id="' . $user->id . '" onclick="blockUser(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="slash"></i></a> ';
                } else if ($user->status === Status::UNVERIFIED) {
                    $ul .= '<a data-toggle="tooltip" title="' . trans('admin.verify_action') . '" id="' . $user->id . '" onclick="verifyAccount(this);return false;" href="#" class="on-default remove-row btn btn-warning"><i data-feather="phone"></i></a> ';
                }
                return $ul;
            })
            ->editColumn('status', function ($user) {
                if ($user->status === Status::ACTIVE) {
                    return '<span class="btn btn-success">' . trans('admin.active_status') . '</span>';
                } else if ($user->status === Status::UNVERIFIED) {
                    return '<span class="btn btn-warning" style="font-size: 10px;">' . trans('admin.unverified_status') . '</span>';
                } else if ($user->status === Status::INACTIVE) {
                    return '<span class="btn btn-danger">' . trans('admin.blocked_status') . '</span>';
                }
            })
            ->make(true);
    }

    public static function changeStatus(array $data)
    {
        $user = User::where(['id' => $data['id']])->first();
        if ($user) {
//            if(intval($data['status']) === IsActive::INT_INACTIVE) {
//                $ordersCount = Order::whereIn('status', [
//                    OrderStatus::NEW,
//                    OrderStatus::IN_PROGRESS,
//                    OrderStatus::IN_DELIVERY
//                ])->count();
//                if($ordersCount > 0) {
//                    return trans('admin.user_has_orders_message');
//                }
//            }
            $user->update([
                'status' => $data['status'],
            ]);
            return true;
        }
        return false;
    }


    public static function verifyUser(array $data)
    {
        $user = User::where(['id' => $data['id'], 'status' => Status::UNVERIFIED])->first();
        if ($user) {
            $user->update(['status' => Status::ACTIVE]);
            return true;
        }
        return false;
    }
}

?>
