<?php

class ModelController extends \BaseController {

	protected $oRepo;

	public function __construct(ModelRepository $oRepo) {
		$this->oRepo = $oRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($iSituationId)
	{
		//
		$models = $this->oRepo->getModelsList($iSituationId);
		$aParentTree = $this->oRepo->constructParentTree($iSituationId);
		return View::make('admin.models.index', [
			'models' => $models,
			'situation_id' => $iSituationId,
			'parent_tree' => $aParentTree
		]);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($iSituationId)
	{
		//
		$iSituationId = (int)$iSituationId;
		if(!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
		$aParentTree = $this->oRepo->constructParentTree($iSituationId);
		$durations = Duration::lists('name', 'id');
		return View::make('admin.models.create', [
			'situation_id' => $iSituationId,
			'parent_tree' => $aParentTree,
			'durations' => $durations
		]);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$iSituationId = (int)Input::get('situation_id');
		if(!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($iModelId)
	{
		//
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


}
