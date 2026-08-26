<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class ClinicTimeSlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'max_bookings' => $this->max_bookings,
            'status' => $this->status,
        ];

        if (isset($this->date)) {
            $data['date'] = $this->date;
        }

        if (isset($this->is_available)) {
            $data['is_available'] = (bool) $this->is_available;
        }

        return $data;
    }
}
