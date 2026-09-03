<?php

namespace App\Http\Resources\V2\Official;

use Illuminate\Http\Resources\Json\JsonResource;

class OfficialTeamPlayerResource extends JsonResource
{
    public function toArray($request)
    {
        $attendance = $this->attendance_info ?? null;
        $isRecorded = !empty($attendance);

        return [
            'id'                     => $this->player_id,
            'name'                   => $this->name,
            'number'                 => $this->number,
            'image'                  => $this->image_url,
            'description'            => $this->description ?? '',
            'is_locked'              => $isRecorded,
            'attendance_status'      => $isRecorded ? (int)$attendance['reason_key'] : null,
            'attendance_status_text' => $isRecorded ? $attendance['reason_text'] : null,
            'attendance_notes'       => $isRecorded ? ($attendance['comments'] ?? null) : null,
            'attendance'             => [
                'recorded'           => $isRecorded,
                'is_locked'          => $isRecorded,
                'status_id'          => $isRecorded ? (int)$attendance['reason_key'] : null,
                'status_text'        => $isRecorded ? $attendance['reason_text'] : null,
                'notes'              => $isRecorded ? ($attendance['comments'] ?? null) : null,
                'attendance_row_id'  => $isRecorded ? (int)$attendance['row_id'] : null,
            ],
        ];
    }
}
