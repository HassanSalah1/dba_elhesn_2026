<?php
namespace App\Repositories\Dashboard\Setting;


use App\Models\Gallery;
use App\Repositories\General\UtilsRepository;
use Yajra\DataTables\Facades\DataTables;

class GalleryRepository
{

    // get Galleries and create datatable data.
    public static function getGalleriesData(array $data)
    {
        $gallerys = Gallery::orderBy('id', 'DESC')
            ->where(function ($query) use ($data) {
                if(isset($data['id']) && $data['id']){
                    $query->where([
                        'sport_game_id' => $data['id']
                    ]);
                }
            });
        return DataTables::of($gallerys)
            ->addColumn('type', function ($gallery) {
                return $gallery->image !== null ? trans('admin.Image') : trans('admin.Video');
            })
            ->addColumn('file', function ($gallery) {
                if ($gallery->image && file_exists($gallery->image)) {
                    return '<a href="' . url($gallery->image) . '" data-popup="lightbox">
                                <img src="' . url($gallery->image) . '" class="img-rounded img-preview"
                                style="max-height:50px;max-width:50px;"></a>';
                }else{
                    return '<a href="'.$gallery->video_url.'" target="_blank">'.$gallery->video_url.'</a>';
                }
            })
            ->addColumn('actions', function ($gallery) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $gallery->id . '" onclick="editGallery(this);return false;" href="#" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $gallery->id . '" onclick="deleteGallery(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addGallery(array $data)
    {
        $galleryData = [
            'video_url' => $data['type'] == 'video' ? $data['video_url'] : null,
            'sport_game_id' => isset($data['sport_game_id']) && $data['sport_game_id'] ? $data['sport_game_id'] : null
        ];
        if ($data['type'] == 'image') {
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/galleries/';
            $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id , 327 , 157);
            if ($image !== false) {
                $galleryData['image'] = $image;
            }
        }
        if (count($galleryData) > 0) {
            $created = Gallery::create($galleryData);
            if ($created) {
                return true;
            }
        }
        return false;
    }

    public static function deleteGallery(array $data)
    {
        $gallery = Gallery::where(['id' => $data['id']])->first();
        if ($gallery) {
            if ($gallery->image && file_exists($gallery->image)) {
                unlink($gallery->image);
            }
            $gallery->delete();
            return true;
        }
        return false;
    }

    public static function restoreGallery(array $data)
    {
        $bank = Gallery::where(['id' => $data['id']])->first();
        if ($bank) {
            $bank->restore();
            return true;
        }
        return false;
    }

    public static function getGalleryData(array $data)
    {
        $gallery = Gallery::where(['id' => $data['id']])->first();
        if ($gallery) {
            $gallery->image = $gallery->image ? url($gallery->image) : null;

            if ($gallery->image !== null) {
                $gallery->type = 'image';
            } else {
                $gallery->type = 'video';
            }

            return $gallery;
        }
        return false;
    }

    public static function editGallery(array $data)
    {
        $gallery = Gallery::where(['id' => $data['id']])->first();
        if ($gallery) {
            if ($gallery->image !== null) {
                $data['type'] = 'image';
            } else {
                $data['type'] = 'video';
            }

            $galleryData = [
                'video_url' => $data['type'] == 'video' ? $data['video_url'] : null,
                'sport_game_id' => isset($data['sport_game_id']) && $data['sport_game_id'] ? $data['sport_game_id'] : null
            ];
            if ($data['type'] == 'image') {
                $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
                $image_name = 'image';
                $image_path = 'uploads/galleries/';
                $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id , 327 , 157);
                if ($image !== false) {
                    $galleryData['image'] = $image;
                    if ($gallery->image && file_exists($gallery->image)) {
                        unlink($gallery->image);
                    }
                }
            }
            $updated = $gallery->update($galleryData);
            if ($updated) {
                return true;
            }
        }
        return false;
    }

}

?>
