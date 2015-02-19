<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 16:36
 */
class ModelRepository {

    use Elluminate\Traits\HierarchicalRepository;

    protected $defaultMinThreshold = 75;

    protected $oModel;

    protected $oQuality;

    public function __construct(\Elluminate\Math\LogisticRegression $oModel, \Elluminate\Math\QualityAnalysis $oQuality) {
        $this->oModel = $oModel;
        $this->oQuality = $oQuality;
    }

    public function getModelsList($iSituationId) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oModels = Situation::find($iSituationId)->models()->get(['id', 'name']);
        return $oModels;
    }

    public function getModel($iModelId, $aFields = ['*']) {
        $iModelId = (int)$iModelId;
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oModel = Model::find($iModelId)->first()->get($aFields);
        if(isset($oModel[0]))
            return $oModel[0];
        return $oModel;
    }

    public function computeResult($aInput) {
        $iModelId = $aInput['model_id'];
        $iModelId = (int)$iModelId;
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        unset($aInput['model_id']);
        unset($aInput['_token']);
        $aCovValues = [];
        foreach($aInput as $value) {
            array_push($aCovValues, $value);
        }
        $aFields = ['id', 'coefficients'];
        $oModel = $this->getModel($iModelId, $aFields);
        $aCoefficients = json_decode($oModel->coefficients);
        $fResult = \Elluminate\Math\MathCore::logisticRegression($aCovValues, $aCoefficients);
        return $fResult;
    }

    public function validateUserInput($aInput) {
        $iModelId = (int)$aInput['model_id'];
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oForm = $this->getApplyingForm($iModelId);
        $aRulesAndNames = $this->getUserValidRulesAndNames($oForm);
        $aValidRules = $aRulesAndNames['rules'];
        $aFieldNames = $aRulesAndNames['names'];
        return \Validator::make($aInput, $aValidRules, array(), $aFieldNames);
    }

    private function getUserValidRulesAndNames($oForm) {
        $aValidRules = [];
        $aNames = [];
        foreach($oForm as $aField) {
            $aValidRules[$aField['tech_name']] = 'Required|Numeric';
            $aNames[$aField['tech_name']] = $aField['name'];
        }
        return ['names' => $aNames, 'rules' => $aValidRules];
    }

    public function getApplyingForm($iModelId) {
        $iModelId = (int)$iModelId;
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $aFields = ['id', 'cov_names', 'cov_comments'];
        $oModel = $this->getModel($iModelId, $aFields);
        $aNames = json_decode($oModel->cov_names);
        $aComments = json_decode($oModel->cov_comments);
        $aForm = [];
        foreach($aNames as $key => $value) {
            $sName = $value;
            $sComment = isset($aComments[$key]) ? $aComments[$key] : '';
            array_push($aForm, ['tech_name' => \Elluminate\Engine\E::transliterate($sName), 'name' => $sName, 'comment' => $sComment]);
        }
        unset($aNames);
        unset($aComments);
        return $aForm;
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
        $aTrainingSet = $this->extractTrainingSetFromExcel($fTrainFile);
        $this->oModel->setTrainingSet($aTrainingSet);
        $this->oModel->trainModel();
        $this->oQuality->setModel($this->oModel);
        $this->oQuality->getQualityAnalysis();
        $oModel = new Model();
        $oModel->situation_id = $iSituationId;
        $oModel->name = $sName;
        $oModel->comment = $sComment;
        $oModel->cov_names = json_encode('');
        $oModel->cov_comments = json_encode('');
        $oModel->coefficients = json_encode($this->oModel->getCoefficients());
        $oModel->duration_id = $iDuration;
        $oModel->min_threshold = $iMinThreshold;
        $oModel->core_selection = json_encode('');
        $oModel->threshold = $this->oQuality->getThreshold();
        $oModel->std_coeff = json_encode($this->oQuality->getStdCoeff());
        $oModel->elastic_coeff = json_encode($this->oQuality->getElasticCoeff());
        $oModel->curve_area = $this->oQuality->getCurveArea();
        echo "<XMP>";
        print_r($oModel);
        echo "</XMP>";
        unset($this->oModel);
        unset($this->oQuality);
//        return $oModel->save();
    }

    protected function extractTrainingSetFromExcel($sFileName, $iOffset = 10, $iRowLimit = 0, $iColLimit = 0, $iSheetNumber = 0) {
        $iOffset = (int)$iOffset;
        $iRowLimit = (int)$iRowLimit;
        $iColLimit = (int)$iColLimit;
        $iSheetNumber = (int)$iSheetNumber;
        $aTrainingSet = array();
        try {
            $oInputFileType = PHPExcel_IOFactory::identify($sFileName);
            $oReader = PHPExcel_IOFactory::createReader($oInputFileType);
//            видит непустую ячейку только там, где есть реальное значение, а не стиль и т.д.
            $oReader->setReadDataOnly(true);
            $oPHPExcel = $oReader->load($sFileName);
        }
        catch (\Exception $e) {
            throw new \Elluminate\Exceptions\TrainSetFileException;
        }
        $oSheet = $oPHPExcel->getSheet($iSheetNumber);
//        чтобы не считать ячейки, где есть, например, стиль. должно помочь вместе с setReadDataOnly
//        если заданы ограничения, то читаем до них, иначе - читаем всё, что есть
        $iHighestRow = $iRowLimit ? $iRowLimit : $oSheet->getHighestDataRow();
        $iHighestColumn = $iColLimit ? $iColLimit : $oSheet->getHighestDataColumn();

        for ($iRow = $iOffset; $iRow <= $iHighestRow; $iRow++) {
            $aRowData = $oSheet->rangeToArray('A' . $iRow . ':' . $iHighestColumn . $iRow);
            $aTrainingSet[] = $aRowData[0];
        }
        return $aTrainingSet;
    }
}