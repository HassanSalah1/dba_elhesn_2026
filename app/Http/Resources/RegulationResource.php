<?php

namespace App\Http\Resources;

use App\Repositories\General\UtilsRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class RegulationResource extends JsonResource
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
            'description' => $this->description,
            'file' => $this->file ? url($this->file) : null,
        ];
    }
}
