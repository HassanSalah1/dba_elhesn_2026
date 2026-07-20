<?php
namespace App\Repositories\Dashboard\Setting;


use App\Models\Intro;
use App\Models\Team;
use App\Repositories\General\UtilsRepository;
use Yajra\DataTables\Facades\DataTables;

class TeamRepository
{

    // get Teams and create datatable data.
    public static function getTeamsData(array $data)
    {
        $teams = Team::orderBy('order', 'ASC');
        return DataTables::of($teams)
            ->addColumn('actions', function ($team) {
                $ul = '';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.edit') . '" id="' . $team->id . '" onclick="editTeam(this);return false;" href="#" class="on-default edit-row btn btn-info"><i data-feather="edit"></i></a>
                   ';
                $ul .= '<a data-toggle="tooltip" title="' . trans('admin.delete_action') . '" id="' . $team->id . '" onclick="deleteTeam(this);return false;" href="#" class="on-default remove-row btn btn-danger"><i data-feather="delete"></i></a>';
                return $ul;
            })->make(true);
    }

    public static function addTeam(array $data)
    {
        Team::where('order', '>=', $data['order'])->increment('order');
        $teamData = [
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'position_ar' => $data['position_ar'],
            'position_en' => $data['position_en'],
            'order' => $data['order'],
        ];
        $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
        $image_name = 'image';
        $image_path = 'uploads/teams/';
        $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id , 125 , 125);
        if ($image !== false) {
            $teamData['image'] = $image;
        }
        $created = Team::create($teamData);
        if ($created) {
            return true;
        }
        return false;
    }

    public static function deleteTeam(array $data)
    {
        $team = Team::where(['id' => $data['id']])->first();
        if ($team) {
            $team->delete();
            return true;
        }
        return false;
    }

    public static function restoreTeam(array $data)
    {
        $bank = Team::where(['id' => $data['id']])->first();
        if ($bank) {
            $bank->restore();
            return true;
        }
        return false;
    }

    public static function getTeamData(array $data)
    {
        $team = Team::where(['id' => $data['id']])->first();
        if ($team) {
            $team->image = $team->image ? url($team->image) : null;
            return $team;
        }
        return false;
    }

    public static function editTeam(array $data)
    {
        $team = Team::where(['id' => $data['id']])->first();
        if ($team) {
            if ($data['order'] != $team->order) {
                Team::where('order', '>=', $data['order'])->increment('order');
            }
            $teamData = [
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'position_ar' => $data['position_ar'],
                'position_en' => $data['position_en'],
                'order' => $data['order'],
            ];
            $file_id = 'IMG_' . mt_rand(00000, 99999) . (time() + mt_rand(00000, 99999));
            $image_name = 'image';
            $image_path = 'uploads/teams/';
            $image = UtilsRepository::createImage($data['request'], $image_name, $image_path, $file_id , 125 , 125);
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
