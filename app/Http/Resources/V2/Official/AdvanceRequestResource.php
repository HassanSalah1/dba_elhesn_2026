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
            'team_name'      => $this->user_team ? ($this->user_team->full_team_name ?: ($this->user_team->team ? $this->user_team->team->name : null)) : null,
            'user_id'        => $this->user_id,
            'players_count'  => (int) $this->players_count,
            'escorts_count'  => (int) $this->escorts_count,
            'cost'           => (float) $this->cost,
            'location'       => $this->location,
            'statement'      => $this->statement,
            'details'        => $this->details,
            'tournament'     => $this->tournament,
            'match_timing'   => $this->match_timing,
            'move_date'      => $this->move_date,
            'return_date'    => $this->return_date,
            'breakfast'      => $this->breakfast,
            'lunch'          => $this->lunch,
            'dinner'         => $this->dinner,
            'snacks'         => $this->snacks,
            'type'           => $this->type ?: 'سلفة',
            'status'         => $this->status ?: 'pending',
            'synced'         => (bool) $this->synced_to_sqlserver,
            'created_at'     => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
