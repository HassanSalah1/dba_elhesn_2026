<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $employee = \App\Models\HrEmployee::where('user_id', $this->id)->with('category')->first();
        if (!$employee && $this->user_id) {
            $employee = \App\Models\HrEmployee::where('row_id', $this->user_id)->with('category')->first();
        }

        $data = [
            'id'          => $this->id,
            'name'        => $this->name,
            'phone'       => $this->phone,
            'email'       => $this->email,
            'edit_email'  => $this->edited_email,
            'is_verified' => ($this->edited_email !== null) ? false : true,
            'image'       => ($this->image !== null) ? url($this->image, [], true) : null,
            'role'        => $this->role,
            'is_hr_admin' => $employee ? (bool)($employee->hr_admin ?? false) : false,
            'is_medical'  => $this->isMedical(),
        ];

        if ($employee) {
            $data['employee'] = new \App\Http\Resources\V2\HrEmployeeResource($employee);
        }

        return $data;
    }
}
