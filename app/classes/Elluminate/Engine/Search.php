<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 18.02.15
 * Time: 14:32
 */
namespace Elluminate\Engine;

class Search {

    private $sSearchText;

    const GROUP_LIMIT = 10;

    /**
     * @return mixed
     */
    public function getOkvedCodeResult()
    {
        return $this->aOkvedCodeResult;
    }

    /**
     * @return mixed
     */
    public function getSituationNameResult()
    {
        return $this->aSituationNameResult;
    }

    /**
     * @return mixed
     */
    public function getModelNameResult()
    {
        return $this->aModelNameResult;
    }

    private $aOkvedCodeResult;

    private $aSituationNameResult;

    private $aModelNameResult;

    private $bOkvedCodeOverlimit;

    private $bSituationNameOverlimit;

    private $bModelNameOverLimit;

    /**
     * @param mixed $sSearchText
     */
    public function setSearchText($sSearchText)
    {
        $this->sSearchText = $sSearchText;
    }

    public function __construct() {
        $this->aOkvedCodeResult = $this->aSituationNameResult = $this->aModelNameResult = [];
        $this->bModelNameOverLimit = $this->bOkvedCodeOverlimit = $this->bSituationNameOverlimit = false;
    }

    public function startSearch() {
        $this->aModelNameResult = $this->findByModelName();
        $this->aSituationNameResult = $this->findBySituationName();
        $this->aOkvedCodeResult = $this->findByOkvedCode();
    }

    private function findByModelName() {
        $iCount = \Model::where('name', 'LIKE', "%$this->sSearchText%")->count('id');
        if($iCount > self::GROUP_LIMIT)
            $this->bModelNameOverLimit = true;
        return \Model::where('name', 'LIKE', "%$this->sSearchText%")->take(self::GROUP_LIMIT)->get(['id', 'name']);
    }

    private function findBySituationName() {
        $iCount = \Situation::where('name', 'LIKE', "%$this->sSearchText%")->count('id');
        if($iCount > self::GROUP_LIMIT)
            $this->bSituationNameOverlimit = true;
        return \Situation::where('name', 'LIKE', "%$this->sSearchText%")->take(self::GROUP_LIMIT)->get();
    }

    /**
     * @return mixed
     */
    public function getOkvedCodeOverlimit()
    {
        return $this->bOkvedCodeOverlimit;
    }

    /**
     * @return mixed
     */
    public function getSituationNameOverlimit()
    {
        return $this->bSituationNameOverlimit;
    }

    /**
     * @return mixed
     */
    public function getModelNameOverLimit()
    {
        return $this->bModelNameOverLimit;
    }

    private function findByOkvedCode() {
        $iCount = \Situation::where('okved_correspondence', 'LIKE', "%$this->sSearchText%")->count('id');
        if($iCount > self::GROUP_LIMIT)
            $this->bSituationNameOverlimit = true;
        return \Situation::where('okved_correspondence', 'LIKE', "%$this->sSearchText%")->get();
    }
}