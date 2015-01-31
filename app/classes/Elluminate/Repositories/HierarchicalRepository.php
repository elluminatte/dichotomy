<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 16:57
 */

namespace Elluminate\Repositories;

/** трейт для общих методов моделей и ситуаций
 * Class HierarchicalRepository
 * @package Elluminate\Repositories
 */
trait HierarchicalRepository {

    /** собирает дерево ситуаций по id
     * @param $iSituationId - id ситуации, для которой нужно собрать дерево
     * @return array - дерево ситуаций
     */
    public function constructParentTree($iSituationId) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId) return array();
        $aParentTree = array();
        $oSituation = \Situation::find($iSituationId);
        if(!$oSituation) return array();
        array_push($aParentTree, ['id' => $oSituation->id, 'name' => $oSituation->name]);
        while($oSituation->parent()->get()->first()) {
            $oParent = $oSituation->parent()->get()->first();
            array_unshift($aParentTree, ['id' => $oParent->id, 'name' => $oParent->name]);
            $oSituation = $oParent;
        }
        return $aParentTree;
    }
}