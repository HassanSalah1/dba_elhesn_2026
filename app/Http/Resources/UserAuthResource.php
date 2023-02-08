<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $country = @$this->city->country;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => ($this->image !== null) ? url($this->image, [], true) : null,
            'token' => $this->createToken('damain')->accessToken,
            'role' => $this->role,
        ];
    }
}
