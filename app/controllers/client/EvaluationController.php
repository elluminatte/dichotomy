<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 23.02.15
 * Time: 16:18
 */
class EvaluationController extends \BaseController {

    /**
     * @var EvaluationRepository - репозиторий проведенных вычислений
     */
    protected $oRepo;

    public function __construct(EvaluationRepository $oRepo) {
        $this->oRepo = $oRepo;
    }

    /** показывает список вычислений, которые нуждаются в подтверждении
     * @return \Illuminate\View\View
     */
    public function index() {
        // проверим есть ли у пользователя роль зарег. юзера
        $iUserId = \Auth::user()->id;
        if(!$iUserId || !\Entrust::hasRole('user')) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // возьмем все вычисления, которые соответствуют этому пользователю и не умеют сведений о реальном результате
        $oEvaluations = Evaluation::with('Model')->where('user_id', '=', $iUserId)->whereNull('real_result')->get();
        return View::make('client.evaluations.index', [
            'evaluations' => $oEvaluations
        ]);
    }

    /** показывает форму обратной связи
     * @param $iEvaluationId - id вычисления, для которого выводится форма
     * @return \Illuminate\View\View
     */
    public function show($iEvaluationId) {
        $oForm = $this->oRepo->getConfirmationForm($iEvaluationId);
        $iModelId = Evaluation::find($iEvaluationId, ['id', 'model_id'])->model_id;
        $iEstimatedResult = $this->oRepo->getEstimatedResult($iEvaluationId);
        return View::make('client.evaluations.detail', [
            'form' => $oForm,
            'iEvaluationId' => $iEvaluationId,
            'estimated_result' => $iEstimatedResult,
            'model_id' => $iModelId
        ]);
    }

    /**
     * осуществляет обратную связь
     */
    public function confirm() {
        $iRealResult = (int)Input::get('real_result');
        $iEvaluationId = (int)Input::get('evaluation_id');
        $oValidation = Evaluation::validate([
            'evaluation_id' => $iEvaluationId,
            'real_result' => $iRealResult
        ]);
        // если валидация провалилась, редеректим обратно с ошибками и заполненными полями
        if ($oValidation->fails())
            return Redirect::route('evaluations.detail', ['iEvaluationId' => $iEvaluationId])->withErrors($oValidation)->withInput();
        $this->oRepo->confirm($iEvaluationId, $iRealResult);
        return View::make('client.evaluations.success');
    }
}