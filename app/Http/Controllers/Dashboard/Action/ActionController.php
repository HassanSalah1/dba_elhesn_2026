<?php

namespace App\Http\Controllers\Dashboard\Action;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Services\Dashboard\Action\ActionService;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    //
    public function showActions()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.actions_title');
        $data['debatable_names'] = array(
            trans('admin.title'),
            trans('admin.image'),
            trans('admin.start_date'),
            trans('admin.end_date'),
            trans('admin.actions')
        );
        return view('admin.action.index')->with($data);
    }

    public function getActionsData(Request $request)
    {
        $data = $request->all();
        return ActionService::getActionsData($data);
    }

    public function showAddAction()
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['title'] = trans('admin.add_action');
        return view('admin.action.add_action')->with($data);
    }

    public function addAction(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return ActionService::addAction($data);
    }

    public function deleteAction(Request $request)
    {
        $data = $request->all();
        return ActionService::deleteAction($data);
    }


    public function showEditAction($id)
    {
        $action = Action::where(['id' => $id])->first();
        if (!$action) {
            return redirect()->to('/admin/actions');
        }
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $action->image = url($action->image()->image);
        $data['action'] = $action;
        $data['title'] = trans('admin.edit_action');
        return view('admin.action.add_action')->with($data);
    }

    public function editAction(Request $request, $id)
    {
        $data = $request->all();
        $data['request'] = $request;
        $data['id'] = $id;
        return ActionService::editAction($data);
    }

    public function removeImage(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;
        return ActionService::removeImage($data);
    }

}
