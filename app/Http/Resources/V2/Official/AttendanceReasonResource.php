<?php

namespace App\Http\Resources\V2\Official;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceReasonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->reason_key,
            'reason_key'    => $this->reason_key,
            'reason'        => $this->reason,
            'the_order'     => $this->the_order,
            'global_reason' => $this->global_reason,
        ];
    }
}
