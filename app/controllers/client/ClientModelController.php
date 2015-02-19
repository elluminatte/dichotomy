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
        $oModels = $this->oRepo->getModelsList($iSituationId);
        $aParentTree = $this->oRepo->constructParentTree($iSituationId);
        return View::make('client.models.index', [
            'models' => $oModels,
//            'situation_id' => $iSituationId,
//            'parent_tree' => $aParentTree
        ]);
    }

    public function apply($iModelId) {
        $oModel = $this->oRepo->getModel($iModelId, ['id', 'name', 'comment']);
        $oForm = $this->oRepo->getApplyingForm($iModelId);
        return View::make('client.models.detail', [
            'model' => $oModel,
            'form' => $oForm
        ]);
    }

    public function compute() {
        $aInput = Input::all();
        $oValidation = $this->oRepo->validateUserInput($aInput);
        if ($oValidation->fails())
            return Redirect::route('tasks.detail', ['iModelId' => '1'])->withErrors($oValidation)->withInput();
        $fResult = $this->oRepo->computeResult($aInput);
    }
}