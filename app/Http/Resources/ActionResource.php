<?php

namespace App\Http\Resources;

use App\Repositories\General\UtilsRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $image = $this->image();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $image ? url($image->image) : url('images/default.png'),
            'start_date' => $this->start_date ? date('c', strtotime($this->start_date)) : null,
            'end_date' => $this->end_date ? date('c', strtotime($this->end_date)) : null,
            'location' => $this->location,
            'created_date' => UtilsRepository::translateDate(date('d F Y', strtotime($this->created_at))),
        ];
    }
}
