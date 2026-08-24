<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class HrDocumentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'employee_row_id' => $this->employee_row_id,
            'description'     => $this->description,
            'attachment_url'  => $this->attachment_path ? asset($this->attachment_path) : null,
            'employee'        => $this->employee ? [
                'id'        => $this->employee->id,
                'row_id'    => $this->employee->row_id,
                'name'      => app()->getLocale() == 'en' ? ($this->employee->name_en ?: $this->employee->name_ar) : $this->employee->name_ar,
                'name_ar'   => $this->employee->name_ar,
                'name_en'   => $this->employee->name_en,
                'job_title' => $this->employee->job_title,
                'photo'     => $this->employee->photo ? (str_starts_with($this->employee->photo, 'http') ? $this->employee->photo : asset($this->employee->photo)) : null,
            ] : null,
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
