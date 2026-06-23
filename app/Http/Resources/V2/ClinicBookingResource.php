<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class ClinicBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booking_number' => $this->id,
            'date' => $this->booking_date,
            'time' => $this->timeSlot ? $this->timeSlot->start_time : null,
            'time_slot' => new ClinicTimeSlotResource($this->timeSlot),
            'status' => $this->status,
            'status_text' => trans('api.booking_status_' . $this->status),
            'injury_type' => $this->injury_type,
            'description' => $this->description,
            'is_for_other' => $this->is_for_other,
            'other_name' => $this->other_name,
            'other_phone' => $this->other_phone,
            'other_country_code' => $this->other_country_code,
            'attachments' => ClinicAttachmentResource::collection($this->attachments),
        ];
    }
}
