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
            'goals'          => $this->goals,
            'wins'           => $this->wins,
            'losses'         => $this->losses,
            'matches_played' => $this->matches_played,
            'minutes_played' => $this->minutes_played,
            'yellow_cards'   => $this->yellow_cards,
            'red_cards'      => $this->red_cards,
        ];
    }
}
