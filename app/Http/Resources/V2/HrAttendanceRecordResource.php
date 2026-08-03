<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class HrAttendanceRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'row_id'          => $this->row_id,
            'employee_row_id' => $this->employee_row_id,
            'attendance_date' => $this->attendance_date,
            'check_in_time'   => $this->check_in_time,
            'check_out_time'  => $this->check_out_time,
            'status'          => $this->status, // 1:Present, 2:Absent, 3:Leave, 4:Holiday
            'notes'           => $this->notes,
        ];
    }
}
