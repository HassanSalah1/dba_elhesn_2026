<?php

namespace App\Http\Resources;

use App\Entities\Key;
use App\Entities\OrderType;
use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

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
        $lang = App::getLocale();
        if ($this->title_ar !== null) {
            $title = $lang === 'en' ? $this->title_en : $this->title_ar;
            $message = $lang === 'en' ? $this->message_en : $this->message_ar;
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
            'type' => $this->type,
            'seen' => $this->seen
        ];
    }
}
