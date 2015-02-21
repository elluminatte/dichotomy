<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 28.12.14
 * Time: 1:07
 */

namespace Elluminate\Math;


/** аппарат логистической регрессии
 * Class LogisticRegression
 * @package Elluminate\Math
 */
class LogisticRegression
{

    /**
     * максимальное кол-во регрессоров
     */
    const  MAX_COVARIATES_NUM = 12;

    /**
     * максимальное кол-во экспериментов
     */
    const MAX_EXPERIMENTS_NUM = 10000;

    /**
     * минимальное кол-во регрессоров
     */
    const MIN_COVARIATES_NUM = 1;

    /**
     * степень покрытия регрессоров экспериментами
     */
    const EXPERIMENTS_COVERAGE = 10;

    /**
     * максимальное кол-во раз ухудшения найденных коэффициентов, после которого можно считать, что метод расходится
     */
    const MAX_WORSE_TIMES = 4;

    /**
     * максималное кол-во итераций метода Ньютона-Рафсона
     */
    const MAX_NEWTON_RAPHSON_ITERATIONS = 1000;

    /**
     * точность метода Ньютона-Рафсона
     */
    const EPSILON = 0.001;

    /**
     * величина скачка метода Ньютона-Рафсона, когда считаем, что метод разошелся
     */
    const JUMP_VALUE = 1000;

    /**
     * минимальный порог отсечения по умолчанию
     */
    const DEFAULT_MIN_THRESHOLD = 75;

    /**
     * @var MathCore - математическое ядро
     */
    protected $oMath;

    /**
     * @var - кол-во регрессоров
     */
    private $iCovariatesNum;

    /**
     * @var - кол-во экспериментов
     */
    private $iExperimentsNum;

    /**
     * @var - массив регрессоров
     */
    private $aCovariates;

    /**
     * @var - вектор значений регрессии
     */
    private $aRegression;

    /**
     * @var - вектор коэффициентов модели
     */
    private $aCoefficients;


    /** геттер для коэффициентов моделт
     * @return mixed
     */
    public function getCoefficients()
    {
        return $this->aCoefficients;
    }

    /** геттер вектора значений функции
     * @return mixed
     */
    public function getRegression()
    {
        return $this->aRegression;
    }

    /** геттер для регрессоров
     * @return mixed
     */
    public function getCovariates()
    {
        return $this->aCovariates;
    }

    public function __construct(MathCore $oMath)
    {
        // внедрли зависимость - мат. ядро
        $this->oMath = $oMath;
    }

    /** внедряет обучающую выборку, если она прошла проверку
     * @param $aTrainingSet - массив с обучающей выборкой
     * @throws \Elluminate\Exceptions\TrainSetFileException
     */
    public function setTrainingSet($aTrainingSet)
    {
        // проверим выборку на соответствие нашим правилам
        $bCheckResult = $this->checkTrainingSet($aTrainingSet);
        // если проверка прошла успешно, то выделяем регрессоры и функцию
        if ($bCheckResult) {
            $this->separateRegressionAndCovariates($aTrainingSet);
        }
        unset($aTrainingSet);
    }

    /** разделяет регрессоры и значения функции
     * @param $aTrainingSet - массив обучающей выборки
     * @return bool - результат разделения
     */
    private function separateRegressionAndCovariates($aTrainingSet)
    {
        $aRegression = [];
        $aCovariates = [];
        foreach ($aTrainingSet as $key => $value) {
            // регрессия - первый столбец
            $aRegression[] = $value[0];
            // т.к. коэффициентов на 1 больше (есть свободный член), то надо заполнить первый столбец
            // регрессоров искусственными данными, рекомендует едини
            $value[0] = 1;
            // регрессоры - все остальное
            $aCovariates[] = $value;
        }
        unset($aTrainingSet);
        $this->aCovariates = $aCovariates;
        unset($aCovariates);
        $this->aRegression = $aRegression;
        unset($aRegression);
        return true;
    }

    /** обучаем модель, ищем коэффициенты
     * @throws \Elluminate\Exceptions\SingularException
     */
    public function trainModel()
    {
        $aCoefficients = $this->computeCoefficients();
        if ($aCoefficients && count($aCoefficients))
            $this->aCoefficients = $aCoefficients;
    }

    /** ищет коэффициенты регрессии методом Ньютона-Рафсона
     * @return array|bool - вектор коэффициентов или false, если не получилось
     * @throws \Elluminate\Exceptions\MathException
     * @throws \Elluminate\Exceptions\SingularException
     */
    public function computeCoefficients()
    {
        // создаем начальное приближение
        $aCoeffVector = $this->oMath->constructVector($this->iCovariatesNum + 1);
        // эти коэффициенты на данный момент лучшие
        $aFinalCoeffVector = $aCoeffVector;
        // посчитаем значение функции при таких коэффициентах
        $aActualRegVector = $this->constructActualFuncVector($this->aCovariates, $aCoeffVector);
        // посчитаем СКО между нашими расчетами и функцией из обучающей выборки
        $fMse = $this->oMath->meanSqError($aActualRegVector, $this->aRegression);
        // пока коэффициенты хуже не стали
        $iWorseTimes = 0;
        for ($i = 0; $i < self::MAX_NEWTON_RAPHSON_ITERATIONS; ++$i) {
            // посчситаем новый вектор коэффициентов
            $aNextCoeffVector = $this->constructNextCoeffVector($aCoeffVector, $this->aCovariates, $this->aRegression, $aActualRegVector);
            // там может не найтись обратной матрицы, тогда обучение не удалось, так может случиться в редких случаях
            if (!$aNextCoeffVector) throw new \Elluminate\Exceptions\SingularException;
            // если на последующем шаге коэффициенты не изменились с заданной точностью, то считаем, что нашли лучшие
            if (!$this->checkChanging($aCoeffVector, $aNextCoeffVector)) return $aFinalCoeffVector;
            // если произошел прыжок, то метод начал расходиться, отдадим коэффициенты, которые получили до расхождения, они получше должны быть
            if ($this->checkJumping($aCoeffVector, $aNextCoeffVector)) return $aFinalCoeffVector;
            // посчитаем функцию с коэффициентами, которые лучшие к этому шагу
            $aActualRegVector = $this->constructActualFuncVector($this->aCovariates, $aNextCoeffVector);
            // проверим СКО
            $fNextMse = $this->oMath->meanSqError($aActualRegVector, $this->aRegression);
            // если СКО стало больще
            if ($fNextMse > $fMse) {
                // значит мы ухудшаем наши коэффициенты
                ++$iWorseTimes;
                // если ухудшаем уже долго, то пора вернуть хоть какие-то, пока не стало совсем плохо
                if ($iWorseTimes > self::MAX_WORSE_TIMES)
                    return $aFinalCoeffVector;
//                $aCoeffVector = $aNextCoeffVector;
                // если ухудшаем не очень давно, то возьмем средние коэффициенты между шагами, может метод еще сойдется
                for ($k = 0; $k < count($aCoeffVector); ++$k)
                    $aCoeffVector[$k] = ($aCoeffVector[$k] + $aNextCoeffVector[$k]) / 2;
            } else {
                // если коэффициенты стали лучше
                $aCoeffVector = $aNextCoeffVector;
                $aFinalCoeffVector = $aCoeffVector;
                // обнулим счетчик ухудшений
                $iWorseTimes = 0;
            }
            // обновим СКО для следующей итерации
            $fMse = $fNextMse;
        }
        return $aFinalCoeffVector;
    }

    /** проверяет вектор коэффициентов на наличие изменений
     * @param $aOldCoeffVector - старый вектор
     * @param $aNewCoeffVector - новый вектор
     * @return bool - истина, если изменения есть; иначе - ложь
     */
    private function checkChanging($aOldCoeffVector, $aNewCoeffVector)
    {
        $iRows = count($aOldCoeffVector);
        for ($i = 0; $i < $iRows; ++$i)
            if (abs($aOldCoeffVector[$i] - $aNewCoeffVector[$i]) > self::EPSILON)
                return true;
        return false;
    }

    /** проверяет не произошел ли прыжок из-за расхождения метода
     * @param $aOldCoeffVector - старый вектор коэффициентов
     * @param $aNewCoeffVector - новый вектор коэффициентов
     * @return bool - истина, если произошел прыжок, иначе - ложь
     */
    private function checkJumping($aOldCoeffVector, $aNewCoeffVector)
    {
        $iRows = count($aOldCoeffVector);
        for ($i = 0; $i < $iRows; ++$i) {
            if ($aOldCoeffVector[$i] === 0) return false;
            if ((abs($aOldCoeffVector[$i] - $aNewCoeffVector[$i]) / abs($aOldCoeffVector[$i])) > self::JUMP_VALUE)
                return true;
        }
        return false;
    }

    /** строит новый вектор коэффициентов методом максимального правдоподобия
     * @param $aCoeffVector - старый вектор коэффициентов
     * @param $aCovMatrix - матрица регрессоров
     * @param $aRegVector - вектор функции
     * @param $aActualRegVector - вектор функцию, которая посчитана с актуальными лучшими коэффицинтами
     * @return array|bool - вектор коэффициентов или ложь, если матрица оказалась сингулярной
     * @throws \Elluminate\Exceptions\MathException
     */
    private function constructNextCoeffVector($aCoeffVector, $aCovMatrix, $aRegVector, $aActualRegVector)
    {
        // X'
        $aTranspCovMatrix = $this->oMath->matrixTranspose($aCovMatrix);
        // WX
        $a = $this->computeXtilde($aActualRegVector, $aCovMatrix);
        // X'WX
        $b = $this->oMath->matrixProduct($aTranspCovMatrix, $a);
        // inverse(X'WX)
        $c = $this->oMath->matrixInverse($b);
        // вдруг там сингулярная матрица, тогда вернем ложь, выше будет перехвачено и выброшено исключение
        if (!$c) return false;
        // inverse(X'WX)X'
        $d = $this->oMath->matrixProduct($c, $aTranspCovMatrix);
        // y-p
        $aSubtract = $this->oMath->vectorSubtraction($aRegVector, $aActualRegVector);
        // inverse(X'WX)X'(y-p)
        $e = $this->oMath->matrixVectorProduct($d, $aSubtract);
        // b + inverse(X'WX)X'(y-p)
        $aResult = $this->oMath->vectorAddition($aCoeffVector, $e);
        return $aResult;
    }

    /** считает логистическую регрессию
     * @param $aCovMatrix - матрица регерссоров
     * @param $aCoeffVector - вектор коэффициентов
     * @return array - вектор логистической регрессии
     */
    private function constructActualFuncVector($aCovMatrix, $aCoeffVector)
    {
        $aResult = $this->oMath->constructVector($this->iExperimentsNum);
        for ($i = 0; $i < $this->iExperimentsNum; ++$i) {
            $z = 0;
            for ($j = 0; $j < $this->iCovariatesNum + 1; ++$j)
                $z += $aCovMatrix[$i][$j] * $aCoeffVector[$j];
            $p = 1 / (1 + exp(-$z));
            $aResult[$i] = $p;
        }
        return $aResult;
    }

    /** проверяет обучающую выборку на соответствие требованиям
     * @param $aTrainingSet - массив обучающей выборки
     * @return bool - результат проверки, истина - все нормально, ложь - что-то не так
     * @throws \Elluminate\Exceptions\TrainSetFileException - исключение
     */
    private function checkTrainingSet($aTrainingSet)
    {
        foreach ($aTrainingSet as $row)
            foreach ($row as $value)
                //если не заполнено или не является числом
                if (!isset($value) || !is_numeric($value))
                    throw new \Elluminate\Exceptions\TrainSetFileException("Проверьте, что все поля обучающей выборки заполнены и являются числами.");
        // один столбец - значение функции, поэтому регрессоров на 1 меньше
        $this->iCovariatesNum = count($aTrainingSet[0]) - 1;
        $this->iExperimentsNum = count($aTrainingSet);
        // проверяем кол-во регрессоров
        if ($this->iCovariatesNum > self::MAX_COVARIATES_NUM || $this->iCovariatesNum < 1)
            throw new \Elluminate\Exceptions\TrainSetFileException("Количество параметров регрессии не должно быть больше " . self::MAX_COVARIATES_NUM);
        // проверяем кол-во экспериментов
        if ($this->iExperimentsNum < $this->iCovariatesNum * self::EXPERIMENTS_COVERAGE || $this->iExperimentsNum > self::MAX_EXPERIMENTS_NUM)
            throw new \Elluminate\Exceptions\TrainSetFileException("Количество опытов должно быть не меньше " . $this->iCovariatesNum * self::EXPERIMENTS_COVERAGE . " и не больше " . self::MAX_EXPERIMENTS_NUM);
        return true;
    }

    /** считает WX для метода максимального правдоподобия
     * @param $aActualRegVector - вектор функции, посчитанный с актуальными коэффициентами
     * @param $aCovMatrix - матрица регрессоров
     * @return array - WX
     * @throws \Elluminate\Exceptions\MathException
     */
    private function computeXtilde($aActualRegVector, $aCovMatrix)
    {
        $pRows = count($aActualRegVector);
        $xRows = count($aCovMatrix);
        $xCols = count($aCovMatrix[0]);
        if ($pRows != $xRows)
            throw new \Elluminate\Exceptions\MathException("Несоответствие размерностей матриц при попытке построить X`");
        $result = $this->oMath->constructMatrix($pRows, $xCols);
        for ($i = 0; $i < $pRows; ++$i)
            for ($j = 0; $j < $xCols; ++$j)
                $result[$i][$j] = $aActualRegVector[$i] * (1 - $aActualRegVector[$i]) * $aCovMatrix[$i][$j];
        return $result;
    }

    /** считает значения функции
     * @return array
     */
    public function estimate()
    {
        if(isset($this->aCoefficients) && count($this->aCoefficients))
            return $this->constructActualFuncVector($this->aCovariates, $this->aCoefficients);
        return [];
    }


}