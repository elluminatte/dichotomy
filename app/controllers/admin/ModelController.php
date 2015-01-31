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
		$oSituationRepo = new SituationRepository();
		$aParentTree = $oSituationRepo->constructParentTree($iSituationId);
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
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
