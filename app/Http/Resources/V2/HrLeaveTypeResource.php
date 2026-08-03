<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class HrLeaveTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'row_id'   => $this->row_id,
            'name_ar'  => $this->name_ar,
            'name_en'  => $this->name_en,
            'name'     => app()->getLocale() == 'en' ? ($this->name_en ?: $this->name_ar) : $this->name_ar,
            'active'   => $this->active,
        ];
    }
}
