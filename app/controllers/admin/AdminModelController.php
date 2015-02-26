<?php

/** Админский контроллер моделей (решаемых задач)
 * Class AdminModelController
 */
class AdminModelController extends \BaseController
{

    /**
     * @var AdminModelRepository - админский репозиторий моделей
     */
    protected $oRepo;

    public function __construct(AdminModelRepository $oRepo)
    {
        $this->oRepo = $oRepo;
    }

    /** список моделей
     * @param $iSituationId - id родительской ситуации
     * @return \Illuminate\View\View
     */
    public function index($iSituationId)
    {
        // получим список моделей
        $oModels = $this->oRepo->getModelsList($iSituationId);

        $aHierarchy = \Elluminate\Engine\E::buildHierarchy($iSituationId);
        // отдадим вид
        return View::make('admin.models.index', [
            'models' => $oModels,
            'situation_id' => $iSituationId,
            'hierarchy' => $aHierarchy
        ]);
    }


    /** покаывает форму добавления новой модели
     * @param $iSituationId - id ситуации, в которую надо добавить
     * @return \Illuminate\View\View
     */
    public function create($iSituationId)
    {
        $iSituationId = (int)$iSituationId;
        if (!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $aHierarchy = \Elluminate\Engine\E::buildHierarchy($iSituationId);
        $oDurations = Duration::lists('name', 'id');
        return View::make('admin.models.create', [
            'situation_id' => $iSituationId,
            'hierarchy' => $aHierarchy,
            'durations' => $oDurations
        ]);
    }


    /** сохраняет новую модель
     * @return $this
     */
    public function store()
    {
        $iSituationId = (int)Input::get('situation_id');
        if (!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $sName = Input::get('name');
        $iDuration = (int)Input::get('duration');
        $iMinThreshold = (int)Input::get('min_threshold');
        $sComment = Input::get('comment');
        $fTrainFile = Input::file('train_file');
        $oValidation = Model::validate([
            'name' => $sName,
            'duration' => $iDuration,
            'min_threshold' => $iMinThreshold,
            'comment' => $sComment,
            'train_file' => $fTrainFile,
            'situation_id' => $iSituationId
        ]);
        // если валидация провалилась, редеректим обратно с ошибками и заполненными полями
        if ($oValidation->fails())
            return Redirect::route('models.create', ['iSituationId' => $iSituationId])->withErrors($oValidation)->withInput();
        $bResult = $this->oRepo->storeModel($iSituationId, $sName, $iDuration, $iMinThreshold, $sComment, $fTrainFile);
        if ($bResult)
            return Redirect::route('models.list', ['iSituationId' => $iSituationId])->withForm_result('addDone');
        else
            return Redirect::route('models.list', ['iSituationId' => $iSituationId])->withForm_result('addFailed');
    }

    /** показывает информацию о модели
     * @param $iModelId - id модели
     * @return \Illuminate\View\View
     */
    public function show($iModelId)
    {
        $oModel = $this->oRepo->getModelDetail($iModelId);
        $aHierarchy = \Elluminate\Engine\E::buildHierarchy($oModel->situation_id);
        return View::make('admin.models.detail', [
            'model' => $oModel,
            'hierarchy' => $aHierarchy
        ]);
    }


    /** удаляет модель
     * @param $iModelId - id модели
     * @return mixed
     */
    public function destroy($iModelId)
    {
        // если нас хотят обмануть или такой модели просто нет, отдаем 404 ошибку
        if (!$iModelId || !Model::find($iModelId)) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // получим родителя удаляемой модели, чтобы знать куда вернуть пользователя при редиректе
        $iSituationId = isset(Model::find($iModelId)->situation()->get()->first()->id) ? (int)Model::find($iModelId)->situation()->get()->first()->id : 0;
        $bResult = $this->oRepo->destroyModel($iModelId);
        // магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата удаления
        if ($bResult)
            return Redirect::route('models.list', ['iSituationId' => $iSituationId])->withForm_result('delDone');
        else
            return Redirect::route('models.list', ['iSituationId' => $iSituationId])->withForm_result('delFailed');
    }

    /** отдает на скачивание файл шаблона обучающей выборки
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate() {
        return Response::download(public_path().'/files/template.xlt');
    }

    /** неактивные по порогу отсечения модели
     * @return \Illuminate\View\View
     */
    public function inactiveModels() {
        $oModels = $this->oRepo->getInactiveModels();
        return View::make('admin.models.inactive', [
            'models' => $oModels
        ]);
    }

    /** отдает на скачивание файл основной обучающей выборки
     * @param $iModelId - id модели
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Elluminate\Exceptions\DumpSelectionException
     */
    public function dump($iModelId) {
        $this->oRepo->dumpSelectionToFile($iModelId);
    }


}
