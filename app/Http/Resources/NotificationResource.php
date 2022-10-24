<?php

namespace App\Http\Resources;

use App\Entities\Key;
use App\Entities\OrderType;
use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $title = null;
        $message = null;
        $user = $this->user;
        if ($this->title_ar !== null) {
            $title = $this->title;
            $message = $this->message;
        } else {
            $title = trans('api.' . $this->title_key);

            $message = str_replace([
                ]
                , [
                ]
                , trans('api.' . $this->message_key));
        }

        return [
            'id' => $this->id,
            'title' => $title,
            'message' => $message,
            'time' => date('h:i a', strtotime($this->created_at)),
            'date' => date('Y/m/d', strtotime($this->created_at)),
            'type' => $this->type
        ];
    }
}
