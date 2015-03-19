<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 18.02.15
 * Time: 14:30
 */
class SearchController extends BaseController {

    protected $oSearch;

    public function __construct(\Elluminate\Engine\Search $oSearch) {
        $this->oSearch = $oSearch;
    }

    public function index() {
        // получим поисковую фразу
        $sSearchText = Input::get('search_text', '');
        $this->oSearch->setSearchText($sSearchText);
        $this->oSearch->startSearch();
        // возьмем все нужные результаты поиска
        $aOkvedCodeResult = $this->oSearch->getOkvedCodeResult();
        $aSituationNameResult = $this->oSearch->getSituationNameResult();
        $aModelNameResult = $this->oSearch->getModelNameResult();
        $bCodeOverlimit = $this->oSearch->getOkvedCodeOverlimit();
        $bSitNameOverlimit = $this->oSearch->getSituationNameOverlimit();
        $bModelNameOverlimit = $this->oSearch->getModelNameOverLimit();
        // и отдадим в вид
        return View::make('client.search.index',[
            'okved_code' => $aOkvedCodeResult,
            'code_overlimit' => $bCodeOverlimit,
            'situation_name' => $aSituationNameResult,
            'situation_overlimit' => $bSitNameOverlimit,
            'model_name' => $aModelNameResult,
            'model_overlimit' => $bModelNameOverlimit
        ]);
    }
}