<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 25.02.15
 * Time: 12:13
 */
class AdminModelRepository extends ModelRepository {

    /** получает модель и информацию о ней
     * @param $iModelId - id модели
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null|static
     */
    public function getModelDetail($iModelId) {
        $iModelId = (int)$iModelId;
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // возьмем все поля, которые интересуют администратора
        $aFileds = ['id', 'name', 'comment', 'cov_names', 'cov_comments', 'reg_name', 'reg_comment', 'coefficients', 'min_threshold', 'threshold', 'std_coeff', 'elastic_coeff', 'curve_area', 'durations_id', 'situation_id'];
        // это типа join
        $oModel = Model::with('duration')->find($iModelId, $aFileds);
        // преобразуем в массивы
        $oModel->cov_names = json_decode($oModel->cov_names);
        $oModel->cov_comments = json_decode($oModel->cov_comments);
        $oModel->coefficients = json_decode($oModel->coefficients);
        $oModel->std_coeff = json_decode($oModel->std_coeff);
        $oModel->elastic_coeff = json_decode($oModel->elastic_coeff);
        return $oModel;
    }

    /** получает неактивные по порогу отсечения модели
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInactiveModels() {
        return Model::where('threshold', '<', DB::raw('min_threshold'))->get(['id', 'name', 'situation_id']);
    }

    /** удаляет модель
     * @param $iModelId - id модели
     * @return bool|null - результат удаления
     * @throws Exception
     */
    public function destroyModel($iModelId) {
        $iModelId = (int)$iModelId;
        // проверяем не хотят ли нас обмануть - существует ли такая модель
        if (!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // удаляем сущность
        return Model::find($iModelId)->delete();
    }

    /** сохраняет новую модель
     * @param $iSituationId - id родительской ситуации
     * @param $sName - название
     * @param $iDuration - время корректности решения
     * @param $iMinThreshold - минимальный порог отсечения
     * @param $sComment - комментарий
     * @param $fTrainFile - файл с обучающей выборкой
     * @return bool
     * @throws \Elluminate\Exceptions\InstanceException
     * @throws \Elluminate\Exceptions\TrainSetFileException
     */
    public function storeModel($iSituationId, $sName, $iDuration, $iMinThreshold, $sComment, $fTrainFile) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $sName = (string)$sName;
        $iDuration = (int)$iDuration;
        $iMinThreshold = ($iMinThreshold <= 100 && $iMinThreshold >=50) ? (int)$iMinThreshold : \Elluminate\Math\LogisticRegression::DEFAULT_MIN_THRESHOLD;
        $sComment = (string)$sComment;
        // получим из файла все, что нам нужно
        $aFileContent = $this->extractDataFromExcel($fTrainFile);
        // преобразуем содержимое в нужную форму
        $this->prepareFileContent($aFileContent);
        // заберем оттуда саму выборку
        $aTrainingSet = array_slice($aFileContent, 2);
        // возьмем название регрессии
        $aRegName = $aFileContent[0][0];
        // единицы измерения регрессии
        $aRegComment = $aFileContent[1][0];
        // названия регрессоров
        $aCovNames = array_slice($aFileContent[0], 1);
        // единицы измерения регрессоров
        $aCovComments = array_slice($aFileContent[1], 1);
        unset($aFileContent);
        // зададим ввыборку для модели
        $this->oModel->setTrainingSet($aTrainingSet);
        // обучим модель
        $this->oModel->trainModel();
        // зададим модель для анализа качества
        $this->oQuality->setModel($this->oModel);
        // проведем анализ качества
        $this->oQuality->getQualityAnalysis();
        // создадим новую сущность БД и забьем атрибуты
        $oModel = new Model();
        $oModel->situation_id = $iSituationId;
        $oModel->name = $sName;
        $oModel->comment = $sComment;
        // нам не надо экранировать символы юникода, это только увеличит размер поля
        $oModel->cov_names = json_encode($aCovNames, JSON_UNESCAPED_UNICODE);
        $oModel->cov_comments = json_encode($aCovComments, JSON_UNESCAPED_UNICODE);
        $oModel->reg_name = $aRegName;
        $oModel->reg_comment = $aRegComment;
        $oModel->coefficients = json_encode($this->oModel->getCoefficients(), JSON_NUMERIC_CHECK);
        $oModel->durations_id = $iDuration;
        $oModel->min_threshold = $iMinThreshold;
        $oModel->core_selection = json_encode($aTrainingSet, JSON_NUMERIC_CHECK);
        $oModel->threshold = $this->oQuality->getThreshold();
        $oModel->std_coeff = json_encode($this->oQuality->getStdCoeff(), JSON_NUMERIC_CHECK);
        $oModel->elastic_coeff = json_encode($this->oQuality->getElasticCoeff(), JSON_NUMERIC_CHECK);
        $oModel->curve_area = $this->oQuality->getCurveArea();
        $oModel->sill = $this->oQuality->getSill();
        // сохраним новую сущность
        return $oModel->save();
    }

    public function updateModel($iModelId, $sName, $iDuration, $iMinThreshold, $sComment, $fTrainFile) {
        $iModelId = (int)$iModelId;
        if(!$iModelId || !Model::find($iModelId)) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oModel = Model::find($iModelId);
        $oModel->name = $sName;
        $oModel->durations_id = $iDuration;
        $oModel->min_threshold = $iMinThreshold;
        $oModel->comment = $sComment;
        if(!is_null($fTrainFile)) {
            // получим из файла все, что нам нужно
            $aFileContent = $this->extractDataFromExcel($fTrainFile);
            // преобразуем содержимое в нужную форму
            $this->prepareFileContent($aFileContent);
            // заберем оттуда саму выборку
            $aTrainingSet = array_slice($aFileContent, 2);
            // возьмем название регрессии
            $aRegName = $aFileContent[0][0];
            // единицы измерения регрессии
            $aRegComment = $aFileContent[1][0];
            // названия регрессоров
            $aCovNames = array_slice($aFileContent[0], 1);
            // единицы измерения регрессоров
            $aCovComments = array_slice($aFileContent[1], 1);
            unset($aFileContent);
            // зададим ввыборку для модели
            $this->oModel->setTrainingSet($aTrainingSet);
            // обучим модель
            $this->oModel->trainModel();
            // зададим модель для анализа качества
            $this->oQuality->setModel($this->oModel);
            // проведем анализ качества
            $this->oQuality->getQualityAnalysis();
            $oModel->cov_names = json_encode($aCovNames, JSON_UNESCAPED_UNICODE);
            $oModel->cov_comments = json_encode($aCovComments, JSON_UNESCAPED_UNICODE);
            $oModel->reg_name = $aRegName;
            $oModel->reg_comment = $aRegComment;
            $oModel->coefficients = json_encode($this->oModel->getCoefficients(), JSON_NUMERIC_CHECK);
            $oModel->durations_id = $iDuration;
            $oModel->min_threshold = $iMinThreshold;
            $oModel->core_selection = json_encode($aTrainingSet, JSON_NUMERIC_CHECK);
            $oModel->threshold = $this->oQuality->getThreshold();
            $oModel->std_coeff = json_encode($this->oQuality->getStdCoeff(), JSON_NUMERIC_CHECK);
            $oModel->elastic_coeff = json_encode($this->oQuality->getElasticCoeff(), JSON_NUMERIC_CHECK);
            $oModel->curve_area = $this->oQuality->getCurveArea();
            $oModel->sill = $this->oQuality->getSill();
            $oModel->oversampling = '';
            Evaluation::where('model_id', '=', $iModelId)->delete();
        }
        return $oModel->save();
    }

    /** приводит содержимое файла обучающей выборки в нужный нам вид
     * @param $aFileContent - содержимое файла обучаюзей выборки
     * @throws \Elluminate\Exceptions\TrainSetFileException
     */
    private function prepareFileContent(&$aFileContent) {
        // проверим, что в массиве что-то есть
        if(!is_array($aFileContent) || !count($aFileContent)) throw new \Elluminate\Exceptions\TrainSetFileException("Ошибка при считывании обучающей выборки");
        // найдем последнюю колонку
        $iLastCol = $this->findLastCol($aFileContent);
        // уберем лишние колонки и вычеркнем неполные строки
        $this->removeExcessColsAndIncomplRows($aFileContent, $iLastCol);
    }

    /** удаляет лишние столбцы и строчки из файла обучающей выборки
     * @param $aFileContent
     * @param $iOffset
     */
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

    /** ищет номер последней значащей колонки в файле
     * @param $aFileContent - содержимое файла
     * @return mixed - номер последней значащей колонки
     */
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

    /** получает данные из файла Excel
     * @param $sFileName - путь к файлу
     * @param int $iOffset - сколько строчек сверху пропускать
     * @param int $iRowLimit - сколько строк прочесть
     * @param int $iColLimit - сколько столбцов прочесть
     * @param int $iSheetNumber - номер листа
     * @return array - данные из файла
     * @throws PHPExcel_Exception
     * @throws \Elluminate\Exceptions\TrainSetFileException
     */
    protected function extractDataFromExcel($sFileName, $iOffset = 12, $iRowLimit = 0, $iColLimit = 0, $iSheetNumber = 0) {
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

    /** пишет основную обучающую выборку в файл
     * @param $iModelId - id модели
     * @return string - путь к файлу
     * @throws \Elluminate\Exceptions\DumpSelectionException
     */
    public function dumpSelectionToFile($iModelId) {
        // получим всё, что нужно для модели
        $oModel = $this->getModel($iModelId, ['id', 'name', 'cov_names', 'cov_comments', 'reg_name', 'core_selection']);
        // преобразуем имя в валидное для имени файла
        $sName = \Elluminate\Engine\E::transliterate($oModel->name);
        try {
            // загрузим шаблон
            $objPHPExcel = PHPExcel_IOFactory::load(public_path() . '/files/dump.xls');
            // будем писать в первый лист
            $objPHPExcel->setActiveSheetIndex(0);
            // запишем имя функции
            $objPHPExcel->getActiveSheet()->SetCellValue('A12', $oModel->reg_name);
            // сформируем массив X1..Xn
            $aCovNums = [];
            $iCovCount = count(json_decode($oModel->cov_names));
            for($i = 1; $i<=$iCovCount; ++$i) {
                array_push($aCovNums, 'X'.$i);
            }
            // запишем строчку X1...Xn
            $objPHPExcel->getActiveSheet()->fromArray($aCovNums, ' ', 'B11');
            // запишем строчку имен регрессоров
            $objPHPExcel->getActiveSheet()->fromArray(json_decode($oModel->cov_names), ' ', 'B12');
            // запишем строчку единиц измерения регрессоров
            $objPHPExcel->getActiveSheet()->fromArray(json_decode($oModel->cov_comments), ' ', 'B13');
            // начнем писать основную обучающую выборку
            $objPHPExcel->getActiveSheet()->fromArray(json_decode($oModel->core_selection), null, 'A14', true);
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            // создадим новый файл по имени функции + текущее дата/время
            $dumpName = $sName . '-' . date("Y-m-d-H-i-s") . '.xls';
            // отдадим всё, что записали в стандартный поток вывода
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$dumpName.'"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            return true;
        }
        catch(\Exception $e) {
            throw new \Elluminate\Exceptions\DumpSelectionException("Ошибка при попытке выгрузить основную обучающую выборку");
        }
    }



}