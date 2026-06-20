<?php

namespace App\Repositories\Dashboard\Setting;

use App\Models\Emergency;
use Yajra\DataTables\Facades\DataTables;

class EmergencyRepository
{
    public static function getEmergenciesData(array $data)
    {
        $emergencies = Emergency::orderBy('order', 'ASC')->get();
        return DataTables::of($emergencies)
            ->addColumn('actions', function ($emergency) {
                $ul = '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $emergency->id . '" onclick="editEmergency(this);return false;" href="#" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a> ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $emergency->id . '" onclick="deleteEmergency(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addEmergency(array $data)
    {
        Emergency::where('order', '>=', $data['order'])->increment('order');
        $created = Emergency::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'phone' => $data['phone'],
            'country_code' => $data['country_code'],
            'order' => $data['order'],
        ]);
        return (bool)$created;
    }

    public static function deleteEmergency(array $data)
    {
        $emergency = Emergency::find($data['id']);
        if ($emergency) {
            $emergency->delete();
            return true;
        }
        return false;
    }

    public static function getEmergencyData(array $data)
    {
        $emergency = Emergency::find($data['id']);
        return $emergency ?: false;
    }

    public static function editEmergency(array $data)
    {
        $emergency = Emergency::find($data['id']);
        if ($emergency) {
            if ($data['order'] != $emergency->order) {
                Emergency::where('order', '>=', $data['order'])->increment('order');
            }
            $updated = $emergency->update([
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'phone' => $data['phone'],
                'country_code' => $data['country_code'],
                'order' => $data['order'],
            ]);
            return (bool)$updated;
        }
        return false;
    }
}
