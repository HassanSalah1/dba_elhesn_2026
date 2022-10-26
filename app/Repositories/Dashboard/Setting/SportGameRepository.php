<?php
namespace App\Repositories\Dashboard\Setting;


use App\Models\SportGame;
use App\Models\Intro;
use App\Repositories\General\UtilsRepository;
use Yajra\DataTables\Facades\DataTables;

class SportGameRepository
{

    // get SportGames and create datatable data.
    public static function getSportGamesData(array $data)
    {
        $teams = SportGame::orderBy('order', 'ASC');
        return DataTables::of($teams)
            ->addColumn('actions', function ($team) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.gallery_title') . '" id="' . $team->id . '" href="'.url('/admin/sport/galleries/' . $team->id).'" class="on-default edit-row btn btn-success"><i data-feather="eye"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $team->id . '" onclick="editSportGame(this);return false;" href="#" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $team->id . '" onclick="deleteSportGame(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addSportGame(array $data)
    {
        SportGame::where('order', '>=', $data['order'])->increment('order');
        $teamData = [
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'description_ar' => $data['description_ar'],
            'description_en' => $data['description_en'],
            'order' => $data['order'],
        ];
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_name = 'image';
        $image_path = 'uploads/sportGames/';
        $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id, 125 , 125);
        if ($image !== false) {
            $teamData['image'] = $image;
        }
        $created = SportGame::create($teamData);
        if ($created) {
            return true;
        }
        return false;
    }

    public static function deleteSportGame(array $data)
    {
        $team = SportGame::where(['id' => $data['id']])->first();
        if ($team) {
            $team->delete();
            return true;
        }
        return false;
    }

    public static function restoreSportGame(array $data)
    {
        $bank = SportGame::where(['id' => $data['id']])->first();
        if ($bank) {
            $bank->restore();
            return true;
        }
        return false;
    }

    public static function getSportGameData(array $data)
    {
        $team = SportGame::where(['id' => $data['id']])->first();
        if ($team) {
            $team->image = $team->image ? url($team->image) : null;
            return $team;
        }
        return false;
    }

    public static function editSportGame(array $data)
    {
        $team = SportGame::where(['id' => $data['id']])->first();
        if ($team) {
            if ($data['order'] != $team->order) {
                SportGame::where('order', '>=', $data['order'])->increment('order');
            }
            $teamData = [
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'],
                'order' => $data['order'],
            ];
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/sportGames/';
            $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id, 125 , 125);
            if ($image !== false) {
                $teamData['image'] = $image;
                if ($team->image && file_exists($team->image)) {
                    unlink($team->image);
                }
            }
            $updated = $team->update($teamData);
            if ($updated) {
                return true;
            }
        }
        return false;
    }

}

?>
