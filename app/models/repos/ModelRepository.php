<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 16:36
 */
class ModelRepository {

    /** математическая модель
     * @var \Elluminate\Math\LogisticRegression
     */
    protected $oModel;

    /** анализатор качества модели
     * @var \Elluminate\Math\QualityAnalysis
     */
    protected $oQuality;

    public function __construct(\Elluminate\Math\LogisticRegression $oModel, \Elluminate\Math\QualityAnalysis $oQuality) {
        $this->oModel = $oModel;
        $this->oQuality = $oQuality;
    }

    /** получает список моделей по id проблемной ситуации
     * @param $iSituationId - id прблемной ситуации
     * @return mixed - список моделей
     */
    public function getModelsList($iSituationId, $bActiveModels = false) {
        $iSituationId = (int)$iSituationId;
        // проверим есть ли такая ситуация
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // отдадим список связанных моделей
        if($bActiveModels)
            $oModels = Situation::find($iSituationId)->activeModels()->get(['id', 'name']);
        else
            $oModels = Situation::find($iSituationId)->models()->get(['id', 'name', 'threshold', 'min_threshold']);
        return $oModels;
    }

    /** получает модель по id
     * @param $iModelId - id модели
     * @param array $aFields - список необходимых для выборки полей
     * @return \Illuminate\Support\Collection|mixed|null|static - модель
     */
    public function getModel($iModelId, $aFields = ['*']) {
        $iModelId = (int)$iModelId;
        // проверим есть ли такая модель
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // найдем ее и вернем
        $oModel = Model::find($iModelId, $aFields);
        if(isset($oModel[0]))
            return $oModel[0];
        return $oModel;
    }
}