<?php

namespace App\Http\Resources;

use App\Models\TeamPlayer;
use Illuminate\Http\Resources\Json\JsonResource;

class SportTeamResource extends JsonResource
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
            'name' => $this->name,
            'image' => $this->image_url,
            'players_count' => TeamPlayer::where(['team_id' => $this->team_id])->count()
        ];
    }
}
