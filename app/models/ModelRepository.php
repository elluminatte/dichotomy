<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 16:36
 */
class ModelRepository {

    use Elluminate\Traits\HierarchicalRepository;

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
    public function getModelsList($iSituationId) {
        $iSituationId = (int)$iSituationId;
        // проверим есть ли такая ситуация
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // отдадим список связанных моделей
        $oModels = Situation::find($iSituationId)->models()->get(['id', 'name']);
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
        $iMinThreshold = ($iMinThreshold <= 100 && $iMinThreshold >=50) ? (int)$iMinThreshold : \Elluminate\Math\LogisticRegression::DEFAULT_MIN_THRESHOLD;
        $sComment = (string)$sComment;
        $aFileContent = $this->extractTrainingSetFromExcel($fTrainFile);
        $this->prepareFileContent($aFileContent);
        $aTrainigSet = array_slice($aFileContent, 2);
        $aRegName = $aFileContent[0][0];
        $aRegComment = $aFileContent[1][0];
        $aCovNames = array_slice($aFileContent[0], 1);
        $aCovComments = array_slice($aFileContent[1], 1);
        unset($aFileContent);
        $this->oModel->setTrainingSet($aTrainigSet);
        $this->oModel->trainModel();
        $this->oQuality->setModel($this->oModel);
        $this->oQuality->getQualityAnalysis();
        $oModel = new Model();
        $oModel->situation_id = $iSituationId;
        $oModel->name = $sName;
        $oModel->comment = $sComment;
        $oModel->cov_names = json_encode($aCovNames, JSON_UNESCAPED_UNICODE);
        $oModel->cov_comments = json_encode($aCovComments, JSON_UNESCAPED_UNICODE);
        $oModel->reg_name = $aRegName;
        $oModel->reg_comment = $aRegComment;
        $oModel->coefficients = json_encode($this->oModel->getCoefficients());
        $oModel->durations_id = $iDuration;
        $oModel->min_threshold = $iMinThreshold;
        $oModel->core_selection = json_encode($aTrainigSet);
        $oModel->threshold = $this->oQuality->getThreshold();
        $oModel->std_coeff = json_encode($this->oQuality->getStdCoeff());
        $oModel->elastic_coeff = json_encode($this->oQuality->getElasticCoeff());
        $oModel->curve_area = $this->oQuality->getCurveArea();
        return $oModel->save();
    }

    private function prepareFileContent(&$aFileContent) {
        // проверим, что в массиве что-то есть
        if(!is_array($aFileContent) || !count($aFileContent)) throw new \Elluminate\Exceptions\TrainSetFileException("Ошибка при считывании обучающей выборки");
        // найдем последнюю колонку
        $iLastCol = $this->findLastCol($aFileContent);
        // уберем лишние колонки и вычеркнем неполные строки
        $this->removeExcessColsAndIncomplRows($aFileContent, $iLastCol);
    }

    private function removeExcessColsAndIncomplRows(&$aFileContent, $iOffset) {
        // пройдем по строкам
        foreach($aFileContent as $rowKey => &$aRow) {
            // отрежем лишние столбцы
            array_splice($aRow, $iOffset);
            // пройдем по столбцам
            foreach($aRow as $key => $value) {
                // первые 2 трогать не будем, там имена и комментарии, их мы уже проверили
                if($rowKey < 2) continue;
                // если в строке есть хотя бы 1 пустой столбец
                if(is_null($value))
                    // то вычеркиваем всю строку
                    unset($aFileContent[$rowKey]);
            }
        }
        // заново проиндексируем массив, чтобы убрать пробелы от вычиркивания строк
        $aFileContent = array_values($aFileContent);
    }

    private function findLastCol($aFileContent) {
        $aEmpty = [];
        // пройдем по первым двум строкам - если нет имени регрессора или названия, то мы его брать не будем - это бессмысленно
        for($iRow = 0; $iRow <= min(count($aFileContent), 1); ++$iRow) {
            foreach($aFileContent[$iRow] as $key => $value) {
                // если значения нет
                if(is_null($value)) {
                    // запишем номер столбца, где пустая строчка
                    array_push($aEmpty, $key);
                    // дальше смотреть не надо, нам нужен минимум, пойдем на следующую строку
                    break;
                }
            }
        }
        // найдем самый левый столбец, где еще есть нужные нам данные
        return min($aEmpty);
    }

    protected function extractTrainingSetFromExcel($sFileName, $iOffset = 12, $iRowLimit = 0, $iColLimit = 0, $iSheetNumber = 0) {
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
        // колонок не должно быть больше, чем максимальное кол-во регрессоров + значение функции
        $iHighestColumn = min($iHighestColumn, \Elluminate\Engine\E::findLetterByPos(\Elluminate\Math\LogisticRegression::MAX_COVARIATES_NUM));
        for ($iRow = $iOffset; $iRow <= $iHighestRow; $iRow++) {
            $aRowData = $oSheet->rangeToArray('A' . $iRow . ':' . $iHighestColumn . $iRow);
            $aTrainingSet[] = $aRowData[0];
        }
        return $aTrainingSet;
    }
}