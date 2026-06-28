<?php
namespace App\Repositories\Dashboard\Setting;


use App\Models\Intro;
use App\Repositories\General\UtilsRepository;
use Yajra\DataTables\Facades\DataTables;

class IntroRepository
{

    // get Intros and create datatable data.
    public static function getIntrosData(array $data)
    {
        $intros = Intro::orderBy('order', 'ASC')->get();
        return DataTables::of($intros)
            ->addColumn('actions', function ($intro) {
                $ul = '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $intro->id . '" onclick="editIntro(this);return false;" href="#" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $intro->id . '" onclick="deleteIntro(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addIntro(array $data)
    {
        $intros = Intro::orderBy('id', 'DESC')->count();
        if ($intros < 3) {
            Intro::where('order', '>=', $data['order'])->increment('order');
            $introData = [
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'],
                'order' => $data['order'],
            ];
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/intros/';
            $introData['image'] = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id);
            if ($introData['image'] !== false) {
                $created = Intro::create($introData);
                if ($created) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function deleteIntro(array $data)
    {
        $intro = Intro::where(['id' => $data['id']])->first();
        if ($intro) {
            $intro->forceDelete();
            return true;
        }
        return false;
    }

    public static function getIntroData(array $data)
    {
        $intro = Intro::where(['id' => $data['id']])->first();
        if ($intro) {
            $intro->image = url($intro->image);
            return $intro;
        }
        return false;
    }

    public static function editIntro(array $data)
    {
        $intro = Intro::where(['id' => $data['id']])->first();
        if ($intro) {
            if ($data['order'] != $intro->order) {
                Intro::where('order', '>=', $data['order'])->increment('order');
            }
            $introData = [
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'],
                'order' => $data['order'],
            ];

            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/intros/';
            $introData['image'] = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id);
            if ($introData['image'] == false) {
                unset($introData['image']);
            } else {
                if ($intro->image && file_exists($intro->image)) {
                    unlink($intro->image);
                }
            }
            $updated = $intro->update($introData);
            if ($updated) {
                return true;
            }
        }
        return false;
    }

}

?>
