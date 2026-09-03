<?php

namespace App\Http\Resources\V2\Official;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvanceRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'row_id'         => $this->row_id,
            'user_team_id'   => $this->user_team_id,
            'team_row_id'    => $this->team_row_id,
            'team_name'      => $this->team_name ?: ($this->user_team ? ($this->user_team->full_team_name ?: ($this->user_team->team ? $this->user_team->team->name : null)) : ($this->sport_team ? $this->sport_team->name_ar : null)),
            'user_id'        => $this->user_id,
            'players_count'  => (int) $this->players_count,
            'escorts_count'  => (int) $this->escorts_count,
            'cost'           => (float) $this->cost,
            'location'       => $this->location,
            'statement'      => $this->statement,
            'details'        => $this->details,
            'tournament'     => $this->tournament,
            'match_timing'   => $this->match_timing,
            'leave_time'     => $this->leave_time,
            'move_date'      => $this->move_date,
            'return_date'    => $this->return_date,
            'breakfast'      => $this->breakfast ?: (string)($this->breakfast_count ?: ''),
            'breakfast_count'=> (int) ($this->breakfast_count ?: $this->breakfast ?: 0),
            'breakfast_cost' => (float) ($this->breakfast_cost ?? 0),
            'lunch'          => $this->lunch ?: (string)($this->lunch_count ?: ''),
            'lunch_count'    => (int) ($this->lunch_count ?: $this->lunch ?: 0),
            'lunch_cost'     => (float) ($this->lunch_cost ?? 0),
            'dinner'         => $this->dinner ?: (string)($this->dinner_count ?: ''),
            'dinner_count'   => (int) ($this->dinner_count ?: $this->dinner ?: 0),
            'dinner_cost'    => (float) ($this->dinner_cost ?? 0),
            'snacks'         => $this->snacks ?: (string)($this->snack_count ?: ''),
            'snack_count'    => (int) ($this->snack_count ?: $this->snacks ?: 0),
            'snack_cost'     => (float) ($this->snack_cost ?? 0),
            'type'           => $this->type ?: 'سلفة',
            'status'         => $this->status ?: 'pending',
            'synced'         => (bool) $this->synced_to_sqlserver,
            'created_at'     => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
