<?php
namespace App\Services\Dashboard\Action;

use App\Repositories\Dashboard\Action\ActionRepository;
use App\Repositories\General\UtilsRepository;
use App\Repositories\General\ValidationRepository;

class ActionService
{


    public static function getActionsData(array $data)
    {
        return ActionRepository::getActionsData($data);
    }

    public static function addAction(array $data)
    {
        $rules = [
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'image' => 'required',
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = ActionRepository::addAction($data);
        return UtilsRepository::response(
            $response,
            trans('admin.process_success_message')
            ,
            trans('admin.success_title')
        );
    }

    public static function deleteAction(array $data)
    {
        $response = ActionRepository::deleteAction($data);
        return UtilsRepository::response(
            $response,
            trans('admin.process_success_message')
            ,
            trans('admin.success_title')
        );
    }

    public static function getActionData(array $data)
    {
        $response = ActionRepository::getActionData($data);
        return UtilsRepository::response($response);
    }

    public static function editAction(array $data)
    {
        $rules = [
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'start_date' => 'required',
            'end_date' => 'nullable',
        ];
        $validated = ValidationRepository::validateWebGeneral($data, $rules);
        if ($validated !== true) {
            return $validated;
        }
        $response = ActionRepository::editAction($data);
        return UtilsRepository::response(
            $response,
            trans('admin.process_success_message')
            ,
            trans('admin.success_title')
        );
    }


    public static function removeImage(array $data)
    {
        $response = ActionRepository::removeImage($data);
        return UtilsRepository::response(
            $response,
            trans('admin.process_success_message')
            ,
            trans('admin.success_title')
        );
    }

}

?>