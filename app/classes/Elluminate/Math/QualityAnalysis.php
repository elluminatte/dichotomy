<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 06.01.15
 * Time: 13:48
 */

namespace Elluminate\Math;

/**
 * Class QualityAnalysis
 * @package Elluminate\Math
 */
class QualityAnalysis
{

    /**
     * @var - модель, качество которой необходимо оценить
     */
    private $oModel;

    /**
     * @var MathCore - класс для работы с математическими функциями
     */
    private $oMath;

    /**
     * @var - вектор специфичности
     */
    private $aSpecificity;

    /**
     * @var - вектор чувствительности
     */
    private $aSensitivity;

    /**
     * @var - вектор эластичных коэффициентов
     */
    private $aElasticCoeff;

    /**
     * @var - вектор стандартизованных коэффициентов
     */
    private $aStdCoeff;

    /**
     * @var - значение в пороге отсечения
     */
    private $fThreshold;

    /**
     * @var - порог отсечения
     */
    private $fSill;

    /**
     * @var - площадь под ROC-кривой
     */
    private $fCurveArea;

    /** геттер эластичных коэффициентов
     * @return mixed
     */
    public function getElasticCoeff()
    {
        return $this->aElasticCoeff;
    }

    /** геттер стандартизованных коэффициентов
     * @return mixed
     */
    public function getStdCoeff()
    {
        return $this->aStdCoeff;
    }

    /** геттер значения в пороге отсечения
     * @return mixed
     */
    public function getThreshold()
    {
        return $this->fThreshold;
    }

    public function getSill() {
        return $this->fSill;
    }

    /** геттер значения площади под ROC-кривой
     * @return mixed
     */
    public function getCurveArea()
    {
        return $this->fCurveArea;
    }

    public function __construct(MathCore $oMath)
    {
        $this->oMath = $oMath;
    }

    /** сеттер для модели
     * @param $oModel
     */
    public function setModel($oModel)
    {
        $this->oModel = $oModel;

    }

    /** анализирует кач-во предоставленной модели
     * @throws \Elluminate\Exceptions\MathException
     */
    public function getQualityAnalysis()
    {
        $this->calcSpecAndSens();
        $this->fThreshold = $this->findThreshold();
        $this->fCurveArea = $this->calcAreaUnderRocCurve();
        $this->findElasticAndStdCoeff();
        unset($this->aSensitivity);
        unset($this->aSpecificity);
        unset($this->oModel);
        unset($this->oMath);
    }

    /** считает чувствительность и специфичность
     * @return bool
     */
    private function calcSpecAndSens()
    {
        // откуда начнем считать
        $iStartPoint = 0;
        // с каким шагом
        $fStep = 0.001;
        // где закончим
        $iEndPoint = 1;
        // посчитаем кол-во отрицательных и положительных исходов в выборке
        $aOutcomes = $this->countOutcomes();
        $iPositives = isset($aOutcomes['positives']) ? $aOutcomes['positives'] : 0;
        $iNegatives = isset($aOutcomes['negatives']) ? $aOutcomes['negatives'] : 0;
        $fRoller = $iStartPoint;
        // посчитаем функцию с нашими коэффициентами
        $aEstimatedFunction = $this->oModel->estimate();
        $j = 0;
        while ($fRoller <= $iEndPoint) {
            // обнулим счетчик ошибок
            $iFalsePositive = $iTruePositive = 0;
            if (is_array($aEstimatedFunction) && count($aEstimatedFunction))
                foreach ($aEstimatedFunction as $key => $value) {
                    // если значение больше, чем текущий порог отсечения
                    if ($value >= $fRoller) {
                        // если значение функции в обучающей выборке 1, то увеличиваем кол-во истинно-положительных
                        if (isset($this->oModel->getRegression()[$key]) && $this->oModel->getRegression()[$key] == 1)
                            $iTruePositive++;
                        else
                        // иначе увеличиваем кол-во ложно положительных
                            $iFalsePositive++;
                    }
                }
            if ($iPositives)
                $this->aSensitivity[$j] = $iTruePositive / $iPositives * 100;
            if ($iNegatives)
                $this->aSpecificity[$j] = $iFalsePositive / $iNegatives * 100;
            $j++;
            $fRoller += $fStep;
        }
        return true;
    }

    /** ищет значение в пороге отсечения
     * @return mixed
     * @throws \Elluminate\Exceptions\MathException
     */
    private function findThreshold()
    {
        // найдем разность векторов чувствительности и 100 - Специфичность
        $aDifference = $this->oMath->matrixDiff($this->oMath->vectorNumberSubtraction($this->aSpecificity, 100), $this->aSensitivity);
        // найдем минимум модуля разности
        $iSill = $this->oMath->findVectorMinimumPos($aDifference);
        $this->fSill = $iSill/1000;
        unset($aDifference);
        // возьмем минимальное из двух значений, чтобы обеспечить качество, не ниже заявленного
        return min($this->oMath->vectorNumberSubtraction($this->aSpecificity, 100)[$iSill], $this->aSensitivity[$iSill]);
    }

    /** считает кол-во положительных и отрицательных исходов
     * @return array - массив с числом исходов
     */
    private function countOutcomes()
    {
        $iNegative = 0;
        $iPositive = 0;
        if (is_array($this->oModel->getRegression()) && count($this->oModel->getRegression()))
            foreach ($this->oModel->getRegression() as $value) {
                if ($value == 1)
                    $iPositive++;
                if ($value == 0)
                    $iNegative++;
            }
        return ['positives' => $iPositive, 'negatives' => $iNegative];
    }

    /** ищет эластичные и стандартизованные коэффициенты
     * @throws \Elluminate\Exceptions\MathException
     */
    private function findElasticAndStdCoeff()
    {
        // для свободного члена такие показатели не ищут, уберем его
        $aCoefficients = array_slice($this->oModel->getCoefficients(), 1);
        $aTranspCov = $this->oMath->matrixTranspose($this->oModel->getCovariates());
        // уберем первый столбец, мы его искусственно заполняли единицами, это свободный член
        $aTranspCov = array_slice($aTranspCov, 1);
        $i = 0;
        $aElasticCoeff = $aStdCoeff = [];
        if (!is_array($aTranspCov) || !count($aTranspCov))
            throw new \Elluminate\Exceptions\MathException("Ошибка при попытке найти мат. ожидание и СКО для регрессоров");
        // найдем среднее для функции
        $aRegMean = $this->oMath->matrixMean($this->oModel->getRegression());
        // найдем СКО для функции
        $aRegStd = $this->oMath->matrixStdDiv($this->oModel->getRegression());
        foreach ($aTranspCov as $value) {
            // найдем среднее по столбцу для регрессоров
            $aCovMean = $this->oMath->matrixMean($value);
            // найдем СКО по столбцу для регрессоров
            $aCovStd = $this->oMath->matrixStdDiv($value);
            // посчитаем i-ый эластичный коэффициент
            $aElasticCoeff[$i] = $aCoefficients[$i] * $aCovMean / $aRegMean;
            // посчитаем i-ый стандартизованный коэффициент
            $aStdCoeff[$i] = $aCoefficients[$i] * $aCovStd / $aRegStd;
            $i++;
        }
        unset($aCoefficients);
        unset($aTranspCov);
        if (is_array($aElasticCoeff) && count($aElasticCoeff))
            $this->aElasticCoeff = $aElasticCoeff;
        if (is_array($aStdCoeff) && count($aStdCoeff))
            $this->aStdCoeff = $aStdCoeff;
    }

    /** считает площадь под ROC-кривой
     * @return float - площадь под ROC-кривой
     * @throws \Elluminate\Exceptions\MathException
     */
    private function calcAreaUnderRocCurve()
    {
        $iSensitivityCount = count($this->aSensitivity);
        $iSpecificityCount = count($this->aSpecificity);
        if (($iSensitivityCount != $iSpecificityCount) || !$iSensitivityCount)
            throw new \Elluminate\Exceptions\MathException("Ошибка при попытке посчитать площадь под ROC-кривой");
        $i = 0;
        $fSum = 0;
        // посчитаем интеграл
        while ($i <= $iSensitivityCount - 2) {
            $fSum += abs($this->aSpecificity[$i + 1] - $this->aSpecificity[$i]) * (($this->aSensitivity[$i + 1] + $this->aSensitivity[$i]) / 2);
            $i++;
        }
        // представим в виде 0,00-100,00
        $fArea = round($fSum / 100, 2);
        return $fArea;
    }

}