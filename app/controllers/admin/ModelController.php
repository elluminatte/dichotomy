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
		if(!$iSituationId || !Situation::find($iSituationId, ['id'])) App::abort(404);
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
		if(!$iSituationId || !Situation::find($iSituationId, ['id'])) App::abort(404);
		$sName = Input::get('name');
		$iDuration = (int)Input::get('duration');
		$iMinThresHold = (int)Input::get('min_threshold');
		$sComment = Input::get('comment');
		$fTrainFile = Input::file('train_file');
		$oValidation = Model::validate([
			'name' => $sName,
			'duration' => $iDuration,
			'min_threshold' => $iMinThresHold,
			'comment' => $sComment,
			'train_file' => $fTrainFile,
			'situation_id' => $iSituationId
		]);

		// если валидация провалилась, редеректим обратно с ошибками и заполненными полями
		if ($oValidation->fails())
			return Redirect::route('models.create', ['iSituationId' => $iSituationId])->withErrors($oValidation)->withInput();

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
	public function destroy($id)
	{
		//
	}


}
