<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamPlayerResource extends JsonResource
{
    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        ini_set('serialize_precision', -1);
        $using = $this->merge(static::$using);
        $description = isset($using->data['description']) && $using->data['description'] ?: '';
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image_url,
            'description' => $description
        ];
    }
}
