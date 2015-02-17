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
        echo "<XMP>";
        print_r(json_encode($this->oModel->aCoefficients));
        echo "</XMP>";

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