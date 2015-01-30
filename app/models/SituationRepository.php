<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 30.01.15
 * Time: 15:34
 */
class SituationRepository {
    /**
     * @param $iParentSituationId
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSituationsList($iParentSituationId) {
        $iParentSituationId = (int)$iParentSituationId;
        if($iParentSituationId !==0 && !Situation::find($iParentSituationId, ['id'])) App::abort(404);
        if(!$iParentSituationId)
            $oSituations = Situation::whereNull('parent_id')->get();
        else
            $oSituations = Situation::find($iParentSituationId)->children()->get();
        return $oSituations;
    }

    public function constructParentTree($iSituationId) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId) return array();
        $aParentTree = array();
        $oSituation = Situation::find($iSituationId);
        if(!$oSituation) return array();
        array_push($aParentTree, ['id' => $oSituation->id, 'name' => $oSituation->name]);
        while($oSituation->parent()->get()->first()) {
            $oParent = $oSituation->parent()->get()->first();
            array_unshift($aParentTree, ['id' => $oParent->id, 'name' => $oParent->name]);
            $oSituation = $oParent;
        }
        return $aParentTree;
    }

    public function storeSituation($sName, $sOkvedCorrespondence, $iParentId) {
        $sName = (string)$sName;
        $sOkvedCorrespondence = (string)$sOkvedCorrespondence;
        $iParentId = (int)$iParentId;
        if($iParentId!==0 && !Situation::find($iParentId. ['id'])) App::abort(404);
        $oSituation = new Situation();
        $oSituation->name = $sName;
        $oSituation->okved_correspondence = $sOkvedCorrespondence;
        if($iParentId !== 0)
            $oSituation->parent_id = $iParentId;
        return $oSituation->save();
    }

    public function updateSituation($iSituationId, $sName, $sOkvedCorrespondence) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) App::abort(404);
        $sName = (string)$sName;
        $sOkvedCorrespondence = (string)$sOkvedCorrespondence;
        $oSituation = Situation::find($iSituationId);
        $oSituation->name = $sName;
        $oSituation->okved_correspondence = $sOkvedCorrespondence;
        return $oSituation->save();
    }

    public function destroySection($iSituationId) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) App::abort(404);
        return Situation::find($iSituationId)->delete();
    }


}