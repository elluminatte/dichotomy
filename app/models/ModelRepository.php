<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 16:36
 */
class ModelRepository {

    use Elluminate\Repositories\HierarchicalRepository;

    public function getModelsList($iSituationId) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) App::abort(404);
        $oModels = Situation::find($iSituationId)->models()->get(['id', 'name']);
        return $oModels;
    }
}