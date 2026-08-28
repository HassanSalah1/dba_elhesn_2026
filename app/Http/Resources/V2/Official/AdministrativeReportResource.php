<?php

namespace App\Http\Resources\V2\Official;

use Illuminate\Http\Resources\Json\JsonResource;

class AdministrativeReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'row_id'          => $this->row_id,
            'user_team_id'    => $this->user_team_id,
            'team_name'       => $this->user_team ? ($this->user_team->full_team_name ?: ($this->user_team->team ? $this->user_team->team->name : null)) : null,
            'user_id'         => $this->user_id,
            'official_id'     => $this->official_id,
            'date'            => $this->date ? (is_string($this->date) ? $this->date : $this->date->format('Y-m-d')) : null,
            'subject'         => $this->subject,
            'location'        => $this->location,
            'events'          => $this->events,
            'pros'            => $this->pros,
            'cons'            => $this->cons,
            'recommendations' => $this->recommendations,
            'synced'          => (bool) $this->synced_to_sqlserver,
            'created_at'      => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
