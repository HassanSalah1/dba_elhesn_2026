<?php
namespace App\Repositories\Dashboard\Setting;


use App\Models\Regulations;
use App\Repositories\General\UtilsRepository;
use Yajra\DataTables\Facades\DataTables;

class RegulationRepository
{

    // get Regulations and create datatable data.
    public static function getRegulationsData(array $data)
    {
        $regulations = Regulations::orderBy('order', 'ASC');
        return DataTables::of($regulations)
            ->addColumn('actions', function ($regulation) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $regulation->id . '" onclick="editRegulation(this);return false;" href="#" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $regulation->id . '" onclick="deleteRegulation(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addRegulation(array $data)
    {
        Regulations::where('order', '>=', $data['order'])->increment('order');
        $regulationData = [
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'description_ar' => $data['description_ar'],
            'description_en' => $data['description_en'],
            'order' => $data['order'],
        ];
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $file_name = 'file';
        $file_path = 'uploads/regulations/';
        $file = UtilsRepository::uploadFiles($data['request'], $file_name, $file_path, $file_id);
        if ($file !== false) {
            $regulationData['file'] = $file;
        }
        $created = Regulations::create($regulationData);
        if ($created) {
            return true;
        }
        return false;
    }

    public static function deleteRegulation(array $data)
    {
        $regulation = Regulations::where(['id' => $data['id']])->first();
        if ($regulation) {
            $regulation->delete();
            return true;
        }
        return false;
    }

    public static function restoreRegulation(array $data)
    {
        $bank = Regulations::where(['id' => $data['id']])->first();
        if ($bank) {
            $bank->restore();
            return true;
        }
        return false;
    }

    public static function getRegulationData(array $data)
    {
        $regulation = Regulations::where(['id' => $data['id']])->first();
        if ($regulation) {
            $regulation->file = $regulation->file ? url($regulation->file) : null;
            return $regulation;
        }
        return false;
    }

    public static function editRegulation(array $data)
    {
        $regulation = Regulations::where(['id' => $data['id']])->first();
        if ($regulation) {
            if ($data['order'] != $regulation->order) {
                Regulations::where('order', '>=', $data['order'])->increment('order');
            }
            $regulationData = [
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'],
                'order' => $data['order'],
            ];
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $file_name = 'file';
            $file_path = 'uploads/regulations/';
            $file = UtilsRepository::uploadFiles($data['request'], $file_name, $file_path, $file_id);
            if ($file !== false) {
                $regulationData['file'] = $file;
                if ($regulation->file && file_exists($regulation->file)) {
                    unlink($regulation->file);
                }
            }
            $updated = $regulation->update($regulationData);
            if ($updated) {
                return true;
            }
        }
        return false;
    }

}

?>
