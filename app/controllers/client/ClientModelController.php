<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 18.02.15
 * Time: 14:23
 */

class ClientModelController extends \BaseController {

    protected $oRepo;

    public function __construct(ModelRepository $oRepo)
    {
        $this->oRepo = $oRepo;
    }
    public function index($iSituationId)
    {
        $models = $this->oRepo->getModelsList($iSituationId);
        $aParentTree = $this->oRepo->constructParentTree($iSituationId);
        return View::make('client.models.index', [
            'models' => $models,
//            'situation_id' => $iSituationId,
//            'parent_tree' => $aParentTree
        ]);
    }
}