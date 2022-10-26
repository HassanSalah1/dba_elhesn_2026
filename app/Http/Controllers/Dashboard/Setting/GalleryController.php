<?php

namespace App\Http\Controllers\Dashboard\Setting;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\Setting\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    //
    public function showGalleries($id = null)
    {
        $data['pageConfigs'] = [
            'pageHeader' => false,
            'defaultLanguage' => 'ar',
            'direction' => 'rtl'
        ];
        $data['id'] = $id;
        $data['title'] = trans('admin.gallery_title');
        $data['debatable_names'] = array(trans('admin.type'), trans('admin.file'), trans('admin.actions'));
        return view('admin.settings.gallery')->with($data);
    }

    public function getGalleriesData(Request $request)
    {
        $data = $request->all();
        return GalleryService::getGalleriesData($data);
    }

    public function addGallery(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return GalleryService::addGallery($data);
    }

    public function deleteGallery(Request $request)
    {
        $data = $request->all();
        return GalleryService::deleteGallery($data);
    }

    public function restoreGallery(Request $request)
    {
        $data = $request->all();
        return GalleryService::restoreGallery($data);
    }

    public function getGalleryData(Request $request)
    {
        $data = $request->all();
        return GalleryService::getGalleryData($data);
    }

    public function editGallery(Request $request)
    {
        $data = $request->all();
        $data['request'] = $request;
        return GalleryService::editGallery($data);
    }

}
