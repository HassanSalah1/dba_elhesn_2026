<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class HrLeaveRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'employee_row_id'   => $this->employee_row_id,
            'leave_type_id'     => $this->leave_type_id,
            'leave_type'        => new HrLeaveTypeResource($this->whenLoaded('leaveType')),
            'start_date'        => $this->start_date,
            'end_date'          => $this->end_date,
            'description'       => $this->description,
            'attachment_url'    => $this->attachment_path ? asset($this->attachment_path) : null,
            'status'            => $this->status, // 0:Pending, 1:Approved, 2:Rejected
            'admin_reply_notes' => $this->admin_reply_notes,
            'created_at'        => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
