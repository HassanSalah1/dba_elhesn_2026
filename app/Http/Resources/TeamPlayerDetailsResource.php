<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamPlayerDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->player_id,
            'name'        => $this->name,
            'name_ar'     => $this->name_ar,
            'name_en'     => $this->name_en,
            'number'         => $this->number,
            'image'          => $this->image_url,
            'team_name'      => $this->team ? $this->team->name : null,
            'position'       => $this->position,
            'position_ar'    => $this->position_ar,
            'position_en'    => $this->position_en,
            'birth_date'     => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'age'            => $this->age,
            'nationality'    => $this->nationality,
            'height'         => $this->height,
            'weight'         => $this->weight,
            'goals'          => (int) ($this->goals ?? 0),
            'wins'           => (int) ($this->wins ?? 0),
            'losses'         => (int) ($this->losses ?? 0),
            'matches_played' => (int) ($this->matches_played ?? 0),
            'minutes_played' => (int) ($this->minutes_played ?? 0),
            'yellow_cards'   => (int) ($this->yellow_cards ?? 0),
            'red_cards'      => (int) ($this->red_cards ?? 0),
            'warnings'       => (int) ($this->yellow_cards ?? 0),
        ];
    }
}
