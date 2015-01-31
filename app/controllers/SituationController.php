<?php

class SituationController extends \BaseController {

	protected  $oRepo;

	public function __construct(SituationRepository $oRepo)
	{
		$this->oRepo = $oRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($iParentSituationId = 0)
	{
		//
		$oSituations = $this->oRepo->getSituationsList($iParentSituationId);
		$aParentTree = $this->oRepo->constructParentTree($iParentSituationId);
		return View::make('admin.situations.index', [
			'situations' => $oSituations,
			'parent_situation' => $iParentSituationId,
			'parent_tree' => $aParentTree
		]);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($iParentSituationId = 0)
	{
		//
		$iParentSituationId = (int)$iParentSituationId;
		$aParentTree = $this->oRepo->constructParentTree($iParentSituationId);
		return View::make('admin.situations.create', [
			'parent_situation_id' => $iParentSituationId,
			'parent_tree' => $aParentTree
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
		$sName = Input::get('name');
		$sOkvedCorrespondence = Input::get('okved_correspondence');
		$iParentId = Input::get('parent_id', 0);
		$oValidation = Situation::validate([
			'name' => $sName,
			'okved_correspondence' => $sOkvedCorrespondence,
			'parent_id' => $iParentId
		]);
		if( $oValidation->fails() )
			return Redirect::route('situations.create')->withErrors($oValidation)->withInput();
		$bResult = $this->oRepo->storeSituation($sName, $sOkvedCorrespondence, $iParentId);
		// магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата добавления
		if($bResult)
			return Redirect::route('situations.list', array('iParentSituationId' => $iParentId))->withForm_result('addDone');
		else
			return Redirect::route('situations.list', array('iParentSituationId' => $iParentId))->withForm_result('addFailed');
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($iSituationId)
	{
		//
		$iSituationId = (int)$iSituationId;
		if(!$iSituationId || !Situation::find($iSituationId, ['id'])) App::abort(404);
		$aParentTree = $this->oRepo->constructParentTree($iSituationId);
		$oSituation = Situation::find($iSituationId);
		return View::make('admin.situations.edit', [
			'parent_tree' => $aParentTree,
			'situation' => $oSituation
		]);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		//
		$iSituationId = (int)Input::get('situation_id');
		$sName = Input::get('name');
		$sOkvedCorrespondence = Input::get('okved_correspondence');
		$oValidation = Situation::validate([
			'name' => $sName,
			'id' => $iSituationId
		]);
		$iParentId = (int)Situation::find($iSituationId)->parent()->get()->first()->id;
		if ($oValidation->fails())
//            если валидация провалилась, возвращаемся на форму и показываем ошибки, заполняем поля, чтоб пользователю не писать заново
			return Redirect::route('situations.edit', array('iSituationId' => $iSituationId))->withErrors($oValidation)->withInput();
		$bResult = $this->oRepo->updateSituation($iSituationId, $sName, $sOkvedCorrespondence);
		if($bResult)
			return Redirect::route('situations.list', array('iParentSituationId' => $iParentId))->withForm_result('editDone');
		else
			return Redirect::route('situations.list', array('iParentSituationId' => $iParentId))->withForm_result('editFailed');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($iSituationId)
	{
		//
		if(!$iSituationId || !Situation::find($iSituationId)) App::abort(404);
		$iParentId = (int)Situation::find($iSituationId)->parent()->get()->first()->id;
		$bResult = $this->oRepo->destroySection($iSituationId);
		// магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата удаления
		if($bResult)
			return Redirect::route('situations.list', array('iParentSituationId' => $iParentId))->withForm_result('delDone');
		else
			return Redirect::route('situations.list', array('iParentSituationId' => $iParentId))->withForm_result('delFailed');
	}
}