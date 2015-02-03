<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 16:36
 */
class ModelRepository {

    use Elluminate\Repositories\HierarchicalRepository;

    private $defaultMinThreshold = 75;

    public function getModelsList($iSituationId) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oModels = Situation::find($iSituationId)->models()->get(['id', 'name']);
        return $oModels;
    }

    public function destroyModel($iModelId) {
        $iModelId = (int)$iModelId;
        // проверяем не хотят ли нас обмануть - существует ли такая модель
        if (!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // удаляем сущность
        return Model::find($iModelId)->delete();
    }

    public function storeModel($iSituationId, $sName, $iDuration, $iMinThreshold, $sComment, $fTrainFile) {
        $iSituationId = (int)$iSituationId;
        $sName = (string)$sName;
        $iDuration = (int)$iDuration;
        $iMinThreshold = ($iMinThreshold <= 100 && $iMinThreshold >=0) ? (int)$iMinThreshold : $this->defaultMinThreshold;
        $sComment = (string)$sComment;

    }
}