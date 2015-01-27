<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 27.01.15
 * Time: 15:07
 */
class ModelsController extends BaseController {
    public function showList($sectionId) {
        $sectionId = (int)$sectionId;
        if(!$sectionId) App::abort(404);
        return View::make('admin.models.list');
    }

    public function showModel($modelId) {
        $modelId = (int)$modelId;
        if(!$modelId) App::abort(404);
        return View::make('admin.models.detail');
    }
}