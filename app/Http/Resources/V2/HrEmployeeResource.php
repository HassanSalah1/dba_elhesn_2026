<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class HrEmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'row_id'      => $this->row_id,
            'name_ar'     => $this->name_ar,
            'name_en'     => $this->name_en,
            'name'        => app()->getLocale() == 'en' ? ($this->name_en ?: $this->name_ar) : $this->name_ar,
            'job_title'   => $this->job_title,
            'photo'       => $this->photo ? (str_starts_with($this->photo, 'http') ? $this->photo : asset($this->photo)) : null,
            'username'    => $this->username,
            'category'    => new HrEmployeeCategoryResource($this->whenLoaded('category')),
        ];
    }
}
