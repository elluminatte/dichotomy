<?php

/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 18.02.15
 * Time: 14:23
 */
class ClientModelController extends \BaseController
{

    /**
     * @var ClientModelRepository - репозиторий
     */
    protected $oRepo;

    public function __construct(ClientModelRepository $oRepo)
    {
        $this->oRepo = $oRepo;
    }

    /** показывает список решаемых задач
     * @param $iSituationId - id родительской проблемной ситуации
     * @return \Illuminate\View\View
     */
    public function index($iSituationId)
    {
        $oModels = $this->oRepo->getModelsList($iSituationId, true);
        $aHierarchy = \Elluminate\Engine\E::buildHierarchy($iSituationId);
        return View::make('client.models.index', [
            'models' => $oModels,
            'hierarchy' => $aHierarchy
        ]);
    }

    /** показывет форму для использования модели
     * @param $iModelId - id модели
     * @return \Illuminate\View\View
     */
    public function showModelForm($iModelId)
    {
        $oModel = $this->oRepo->getModel($iModelId, ['id', 'name', 'comment', 'situation_id', 'min_threshold', 'threshold']);
        if($oModel->threshold < $oModel->min_threshold) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $iSituationId = $oModel->situation_id;
        $oForm = $this->oRepo->getApplyingForm($iModelId);
        $aHierarchy = \Elluminate\Engine\E::buildHierarchy($iSituationId);
        return View::make('client.models.detail', [
            'model' => $oModel,
            'form' => $oForm,
            'hierarchy' => $aHierarchy
        ]);
    }

    /** запускает работу модели с введенными пользователем данными
     * @return $this|\Illuminate\View\View
     */
    public function compute()
    {
        $aInput = Input::all();
        $oValidation = $this->oRepo->validateUserInput($aInput);
        if ($oValidation->fails())
            return Redirect::route('tasks.detail', ['iModelId' => $aInput['model_id']])->withErrors($oValidation)->withInput();
        $fResult = $this->oRepo->computeResult($aInput);
        $oModel = $this->oRepo->getModel($aInput['model_id'], ['id', 'reg_name', 'comment', 'situation_id']);
        $sRegName = isset($oModel->reg_name) ? $oModel->reg_name : '';
        $sComment = isset($oModel->comment) ? $oModel->comment : '';
        $aHierarchy = \Elluminate\Engine\E::buildHierarchy($oModel->situation_id);
        return View::make('client.models.result', [
            'result' => $fResult,
            'reg_name' => $sRegName,
            'comment' => $sComment,
            'hierarchy' => $aHierarchy,
            'model_id' => $oModel->id
        ]);
    }
}