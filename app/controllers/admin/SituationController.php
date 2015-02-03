<?php

/** контроллер проблемных ситуаций
 * Class SituationController
 */
class SituationController extends \BaseController
{

    /**
     * @var SituationRepository - репозиторий проблемных ситуаций, там хранится бизнес-логика
     */
    protected $oRepo;

    public function __construct(SituationRepository $oRepo)
    {
        // внедряем зависимость - репозиторий
        $this->oRepo = $oRepo;
    }


    /** показывает список проблемных ситуаций
     * @param int $iParentSituationId - ситуация-родитель, наследников которой надо показать
     * если 0, значит это верхний уровень
     * @return \Illuminate\View\View
     */
    public function index($iParentSituationId = 0)
    {
        // собираем список разделов
        $oSituations = $this->oRepo->getSituationsList($iParentSituationId);
        // собираем дерево родителей для хлебных крошек
        $aParentTree = $this->oRepo->constructParentTree($iParentSituationId);
        // отдаем данные в вид и рисуем его
        return View::make('admin.situations.index', [
            'situations' => $oSituations,
            'parent_situation' => $iParentSituationId,
            'parent_tree' => $aParentTree
        ]);
    }


    /** показывает форму для добавления ситуации
     * @param int $iParentSituationId - id ситуации, внутрь которой будем добавлять
     * если 0, то добавляем на верхний уровень
     * @return \Illuminate\View\View
     */
    public function create($iParentSituationId = 0)
    {
        $iParentSituationId = (int)$iParentSituationId;
        // собираем дерево родителей для хлебных крошек
        $aParentTree = $this->oRepo->constructParentTree($iParentSituationId);
        // отдаем данные в вид и рисуем его
        return View::make('admin.situations.create', [
            'parent_situation_id' => $iParentSituationId,
            'parent_tree' => $aParentTree
        ]);
    }


    /** сохраняет добавленную проблемную ситуацию
     * @return $this - редиректим на список
     */
    public function store()
    {
        // получаем данные из post-массива
        $sName = Input::get('name');
        $sOkvedCorrespondence = Input::get('okved_correspondence');
        $iParentId = Input::get('parent_id', 0);
        // проводим валидацию входных данных
        $oValidation = Situation::validate([
            'name' => $sName,
            'okved_correspondence' => $sOkvedCorrespondence,
            'parent_id' => $iParentId
        ]);
        // если валидация провалилась, редеректим обратно с ошибками и заполненными полями
        if ($oValidation->fails())
            return Redirect::route('situations.create', ['iParentSituationId' => $iParentId])->withErrors($oValidation)->withInput();
        $bResult = $this->oRepo->storeSituation($sName, $sOkvedCorrespondence, $iParentId);
        // магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата добавления
        if ($bResult)
            return Redirect::route('situations.list', ['iParentSituationId' => $iParentId])->withForm_result('addDone');
        else
            return Redirect::route('situations.list', ['iParentSituationId' => $iParentId])->withForm_result('addFailed');
    }


    /** показывает форму редактирования проблемной ситуации
     * @param $iSituationId - id ситуации, которую будем редактировать
     * @return \Illuminate\View\View
     */
    public function edit($iSituationId)
    {
        $iSituationId = (int)$iSituationId;
        // если нас хотят обмануть или такой ситуации просто нет, отдаем 404 ошибку
        if (!$iSituationId || !Situation::find($iSituationId, ['id'])) App::abort(404);
        // собираем дерево родителей для хлебных крошек
        $aParentTree = $this->oRepo->constructParentTree($iSituationId);
        // находим нужную сущность (физически - строка таблицы)
        $oSituation = Situation::find($iSituationId);
        // рисуем вид
        return View::make('admin.situations.edit', [
            'parent_tree' => $aParentTree,
            'situation' => $oSituation
        ]);
    }


    /** сохраняет изменения, внесенные в проблемную ситуацию
     * @return $this - редеректим на список
     */
    public function update()
    {
        $iSituationId = (int)Input::get('situation_id');
        // получаем данные из post-массива
        $sName = Input::get('name');
        $sOkvedCorrespondence = Input::get('okved_correspondence');
        // проводим валидацию входных данных
        $oValidation = Situation::validate([
            'name' => $sName,
            'id' => $iSituationId
        ]);
        // получим родителя редктируемого раздела, чтобы знать куда вернуть пользователя при редиректе
        $iParentId = isset(Situation::find($iSituationId)->parent()->get()->first()->id) ? (int)Situation::find($iSituationId)->parent()->get()->first()->id : 0;
        // если валидация провалилась, редеректим обратно с ошибками и заполненными полями
        if ($oValidation->fails())
            return Redirect::route('situations.edit', ['iSituationId' => $iSituationId])->withErrors($oValidation)->withInput();
        $bResult = $this->oRepo->updateSituation($iSituationId, $sName, $sOkvedCorrespondence);
        // магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата добавления
        if ($bResult)
            return Redirect::route('situations.list', ['iParentSituationId' => $iParentId])->withForm_result('editDone');
        else
            return Redirect::route('situations.list', ['iParentSituationId' => $iParentId])->withForm_result('editFailed');
    }


    /** удаляет проблемную ситуацию
     * @param $iSituationId - id ситуации, которую надо удалить
     * @return mixed
     */
    public function destroy($iSituationId)
    {
        // если нас хотят обмануть или такой ситуации просто нет, отдаем 404 ошибку
        if (!$iSituationId || !Situation::find($iSituationId)) App::abort(404);
        // получим родителя удаляемого раздела, чтобы знать куда вернуть пользователя при редиректе
        $iParentId = isset(Situation::find($iSituationId)->parent()->get()->first()->id) ? (int)Situation::find($iSituationId)->parent()->get()->first()->id : 0;
        $bResult = $this->oRepo->destroySection($iSituationId);
        // магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата удаления
        if ($bResult)
            return Redirect::route('situations.list', ['iParentSituationId' => $iParentId])->withForm_result('delDone');
        else
            return Redirect::route('situations.list', ['iParentSituationId' => $iParentId])->withForm_result('delFailed');
    }
}