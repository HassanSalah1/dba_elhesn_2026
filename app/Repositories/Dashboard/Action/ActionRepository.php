<?php
namespace App\Repositories\Dashboard\Action;


use App\Entities\ImageType;
use App\Models\Action;
use App\Models\Image;
use App\Repositories\General\UtilsRepository;
use Yajra\DataTables\Facades\DataTables;

class ActionRepository
{

    // get Actions and create datatable data.
    public static function getActionsData(array $data)
    {
        $actions = Action::orderBy('start_date', 'DESC')->get();
        return DataTables::of($actions)
            ->editColumn('image', function ($action) {
                if ($action->image()) {
                    return '<a href="' . url($action->image()->image) . '" data-popup="lightbox">
                    <img src="' . url($action->image()->image) . '" class="img-rounded img-preview"
                    style="max-height:50px;max-width:50px;"></a>';
                }
            })
            ->addColumn('actions', function ($action) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $action->id . '" href="' . url('/admin/action/edit/' . $action->id) . '" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $action->id . '" onclick="deleteAction(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addAction(array $data)
    {
        $actionData = [
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'description_ar' => $data['description_ar'],
            'description_en' => $data['description_en'],
            'start_date' => date('Y-m-d', strtotime($data['start_date'])),
            'end_date' => isset($data['end_date']) ? date('Y-m-d', strtotime($data['end_date'])) : null
        ];

        $created = Action::create($actionData);
        if ($created) {
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/actions/';
            $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id, 500, 600);
            if ($image !== false) {
                Image::create([
                    'item_id' => $created->id,
                    'item_type' => ImageType::ACTION,
                    'image' => $image,
                    'primary' => 1
                ]);
            }


            $images = $data['request']->file('images');
            if ($images) {
                foreach ($images as $image) {
                    $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
                    $image_name = $image;
                    $image = UtilsRepository::uploadImage($data['request'], $image_name, $image_path, $file_id, 500, 600);
                    if ($image !== false) {
                        Image::create([
                            'item_id' => $created->id,
                            'item_type' => ImageType::ACTION,
                            'image' => $image,
                            'primary' => 0
                        ]);
                    }
                }
            }


            return true;
        }
        return false;
    }

    public static function deleteAction(array $data)
    {
        $action = Action::where(['id' => $data['id']])->first();
        if ($action) {
            if ($action->image() && file_exists($action->image()->image)) {
                unlink($action->image()->image);
            }
            $action->forceDelete();
            return true;
        }
        return false;
    }

    public static function removeImage(array $data)
    {
        $image = Image::where(['id' => $data['id'], 'item_type' => ImageType::ACTION])->first();
        if ($image) {
            if (file_exists($image->image)) {
                unlink($image->image);
            }
            $image->forceDelete();
            return true;
        }
        return false;
    }

    public static function getActionData(array $data)
    {
        $action = Action::where(['id' => $data['id']])->first();
        if ($action) {
            $action->image = url($action->image);
            return $action;
        }
        return false;
    }

    public static function editAction(array $data)
    {
        $action = Action::where(['id' => $data['id']])->first();
        if ($action) {
            $actionData = [
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'],
                'start_date' => date('Y-m-d', strtotime($data['start_date'])),
                'end_date' => isset($data['end_date']) ? date('Y-m-d', strtotime($data['end_date'])) : null
            ];
            if ($data['request']->hasFile('image')) {
                $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
                $image_name = 'image';
                $image_path = 'uploads/actions/';
                $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id, 500, 600);
                if ($image !== false) {
                    if ($action->image() && file_exists($action->image()->image)) {
                        unlink($action->image()->image);
                    }
                    $imageObj = $action->image();
                    $imageObj->image = $image;
                    $imageObj->save();
                }
            }

            $images = $data['request']->file('images');
            if ($images) {
                foreach ($images as $image) {
                    $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
                    $image_path = 'uploads/actions/';
                    $image_name = $image;
                    $image = UtilsRepository::uploadImage($data['request'], $image_name, $image_path, $file_id, 500, 600);
                    if ($image !== false) {
                        Image::create([
                            'item_id' => $action->id,
                            'item_type' => ImageType::ACTION,
                            'image' => $image,
                            'primary' => 0
                        ]);
                    }
                }
            }

            $updated = $action->update($actionData);
            if ($updated) {
                return true;
            }
        }
        return false;
    }

}

?>