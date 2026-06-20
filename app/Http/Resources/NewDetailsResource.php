<?php

namespace App\Http\Resources;

use App\Repositories\General\UtilsRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class NewDetailsResource extends JsonResource
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
            'description' => $this->description,
            'video_url' => $this->video_url,
            'image' => $image ? url($image->image) : url('images/default.png'),
            'images' => ImageResource::collection($this->images()),
            'category' => $this->category ? $this->category->name : '',
            'created_date' => UtilsRepository::translateDate(date('d F Y', strtotime($this->created_at))),
        ];
    }
}
