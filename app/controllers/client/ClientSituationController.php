<?php

class ClientSituationController extends \BaseController {

	protected $oRepo;

	public function __construct(SituationRepository $oRepo) {
		$this->oRepo = $oRepo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($iParentSituationId = 0)
	{
		// собираем список разделов с моделями
		$oSituations = $this->oRepo->getSituationsList($iParentSituationId, true, true);
//		echo "<XMP>";print_r($oSituations);
//		echo "</XMP>";
		// собираем дерево родителей для хлебных крошек
//		$aParentTree = $this->oRepo->constructParentTree($iParentSituationId);
		// отдаем данные в вид и рисуем его
		return View::make('client.problems.index', [
			'situations' => $oSituations,
//			'parent_situation' => $iParentSituationId,
//			'parent_tree' => $aParentTree
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
