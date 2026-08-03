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
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
