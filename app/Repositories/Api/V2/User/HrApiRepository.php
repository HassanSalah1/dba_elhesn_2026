<?php

namespace App\Repositories\Api\V2\User;

use App\Entities\HttpCode;
use App\Models\HrEmployee;
use App\Models\HrEmployeeCategory;
use App\Models\HrAttendanceRecord;
use App\Models\HrLeaveType;
use App\Models\HrLeaveRequest;
use App\Models\HrDocument;
use App\Models\User;
use App\Http\Resources\V2\HrEmployeeResource;
use App\Http\Resources\V2\HrAttendanceRecordResource;
use App\Http\Resources\V2\HrLeaveTypeResource;
use App\Http\Resources\V2\HrLeaveRequestResource;
use App\Http\Resources\V2\HrDocumentResource;
use App\Repositories\Api\V2\SqlServerApiRepository;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class HrApiRepository
{
    public static function login(array $data)
    {
        $username = $data['username'];
        $password = $data['password'];

        $employee = HrEmployee::where('username', $username)->first();

        if (!$employee || !Hash::check($password, $employee->password_hash)) {
            return [
                'message' => trans('api.invalid_username_or_password') ?: 'بيانات الدخول غير صحيحة',
                'code' => HttpCode::ERROR,
            ];
        }

        // Find or create linked local User
        $user = User::where('id', $employee->user_id)->first();
        if (!$user) {
            $user = User::updateOrCreate(
                ['email' => $username . '@dhclubapp.xyz'],
                [
                    'user_id' => $employee->row_id,
                    'name' => $employee->name_ar ?: ($employee->name_en ?: $username),
                    'password' => Hash::make($password),
                    'role' => 'employee',
                    'status' => 1,
                    'lang' => 'ar',
                ]
            );
            $employee->update(['user_id' => $user->id]);
        }

        $token = $user->createToken('employee_token')->accessToken;

        return [
            'data' => [
                'token' => $token,
                'employee' => new HrEmployeeResource($employee->load('category')),
            ],
            'message' => trans('api.login_success') ?: 'تم تسجيل الدخول بنجاح',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getAttendance(array $data, User $user)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        if (!$employee) {
            return [
                'message' => 'بيانات الموظف غير موجودة',
                'code' => HttpCode::ERROR,
            ];
        }

        $query = HrAttendanceRecord::where('employee_row_id', $employee->row_id);

        $month = isset($data['month']) ? (int) $data['month'] : Carbon::now()->month;
        $year = isset($data['year']) ? (int) $data['year'] : Carbon::now()->year;

        $query->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month);

        $records = $query->orderBy('attendance_date', 'asc')->get();

        $presentCount = $records->where('status', 1)->count();
        $absentCount = $records->where('status', 2)->count();
        $leaveCount = $records->where('status', 3)->count();
        $holidayCount = $records->where('status', 4)->count();

        $totalWorkingDays = $presentCount + $absentCount;
        $percentage = $totalWorkingDays > 0 ? round(($presentCount / $totalWorkingDays) * 100) : 0;

        return [
            'data' => [
                'month' => $month,
                'year' => $year,
                'attendance_percentage' => $percentage,
                'present_days' => $presentCount,
                'absent_days' => $absentCount,
                'leave_days' => $leaveCount,
                'holiday_days' => $holidayCount,
                'records' => HrAttendanceRecordResource::collection($records),
            ],
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getLeaveTypes()
    {
        $types = HrLeaveType::where('active', true)->get();

        return [
            'data' => HrLeaveTypeResource::collection($types),
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function createLeaveRequest(array $data, User $user, $attachmentFile = null)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        if (!$employee) {
            return [
                'message' => 'بيانات الموظف غير موجودة',
                'code' => HttpCode::ERROR,
            ];
        }

        $attachmentPath = null;
        if ($attachmentFile) {
            $dir = public_path('uploads/hr/leave_requests');
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            $fileName = time() . '_' . uniqid() . '.' . $attachmentFile->getClientOriginalExtension();
            $attachmentFile->move($dir, $fileName);
            $attachmentPath = 'uploads/hr/leave_requests/' . $fileName;
        }

        $leaveRequest = HrLeaveRequest::create([
            'employee_row_id' => $employee->row_id,
            'leave_type_id' => $data['leave_type_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'] ?? null,
            'attachment_path' => $attachmentPath,
            'status' => 0, // Pending
        ]);

        // Push real-time to SQL Server
        SqlServerApiRepository::pushSingleHrLeaveRequestToSqlServer($leaveRequest);

        return [
            'data' => new HrLeaveRequestResource($leaveRequest->load('leaveType')),
            'message' => 'تم إضافة طلب الإجازة بنجاح',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getLeaveRequests(User $user)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        if (!$employee) {
            return [
                'data' => [],
                'message' => 'success',
                'code' => HttpCode::SUCCESS,
            ];
        }

        // Always return only the logged-in user's own requests
        $requests = HrLeaveRequest::with(['leaveType', 'employee'])
            ->where('employee_row_id', $employee->row_id)
            ->orderBy('id', 'desc')
            ->get();

        return [
            'data' => HrLeaveRequestResource::collection($requests),
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getAdminLeaveRequests(User $user)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        if (!$employee || !$employee->hr_admin) {
            return [
                'message' => trans('api.unauthorized') ?: 'غير مصرح لك بإجراء هذه العملية',
                'code' => HttpCode::ERROR,
            ];
        }

        // Return all leave requests for all employees
        $requests = HrLeaveRequest::with(['leaveType', 'employee'])
            ->orderBy('id', 'desc')
            ->get();

        return [
            'data' => HrLeaveRequestResource::collection($requests),
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getLeaveRequestDetails($id, User $user)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        $request = HrLeaveRequest::with(['leaveType', 'employee'])->find($id);

        if (!$request) {
            return [
                'message' => 'طلب الإجازة غير موجود',
                'code' => HttpCode::ERROR,
            ];
        }

        // Authorization check: only allow HR admin or the owner
        if ($employee && !$employee->hr_admin && $request->employee_row_id != $employee->row_id) {
            return [
                'message' => trans('api.unauthorized') ?: 'غير مصرح لك بعرض هذا الطلب',
                'code' => HttpCode::ERROR,
            ];
        }

        return [
            'data' => new HrLeaveRequestResource($request),
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function createDocument(array $data, User $user, $attachmentFile = null)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        if (!$employee) {
            return [
                'message' => 'بيانات الموظف غير موجودة',
                'code' => HttpCode::ERROR,
            ];
        }

        $attachmentPath = null;
        if ($attachmentFile) {
            $dir = public_path('uploads/hr/documents');
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            $fileName = time() . '_' . uniqid() . '.' . $attachmentFile->getClientOriginalExtension();
            $attachmentFile->move($dir, $fileName);
            $attachmentPath = 'uploads/hr/documents/' . $fileName;
        }

        $doc = HrDocument::create([
            'employee_row_id' => $employee->row_id,
            'description' => $data['description'],
            'attachment_path' => $attachmentPath,
        ]);

        // Push real-time to SQL Server
        SqlServerApiRepository::pushSingleHrDocumentToSqlServer($doc);

        return [
            'data' => new HrDocumentResource($doc->load('employee')),
            'message' => 'تم إضافة المستند بنجاح',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getDocuments(User $user)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        if (!$employee) {
            return [
                'data' => [],
                'message' => 'success',
                'code' => HttpCode::SUCCESS,
            ];
        }

        // Always return only the logged-in user's own documents
        $docs = HrDocument::with('employee')
            ->where('employee_row_id', $employee->row_id)
            ->orderBy('id', 'desc')
            ->get();

        return [
            'data' => HrDocumentResource::collection($docs),
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getAdminDocuments(User $user)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        if (!$employee || !$employee->hr_admin) {
            return [
                'message' => trans('api.unauthorized') ?: 'غير مصرح لك بإجراء هذه العملية',
                'code' => HttpCode::ERROR,
            ];
        }

        // Return all documents for all employees
        $docs = HrDocument::with('employee')
            ->orderBy('id', 'desc')
            ->get();

        return [
            'data' => HrDocumentResource::collection($docs),
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }

    public static function getDocumentDetails($id, User $user)
    {
        $employee = HrEmployee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = HrEmployee::where('row_id', $user->user_id)->first();
        }

        $doc = HrDocument::with('employee')->find($id);

        if (!$doc) {
            return [
                'message' => 'المستند غير موجود',
                'code' => HttpCode::ERROR,
            ];
        }

        // Authorization check: only allow HR admin or the owner
        if ($employee && !$employee->hr_admin && $doc->employee_row_id != $employee->row_id) {
            return [
                'message' => trans('api.unauthorized') ?: 'غير مصرح لك بعرض هذا المستند',
                'code' => HttpCode::ERROR,
            ];
        }

        return [
            'data' => new HrDocumentResource($doc),
            'message' => 'success',
            'code' => HttpCode::SUCCESS,
        ];
    }
}
