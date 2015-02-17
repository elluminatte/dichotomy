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
class LogisticRegression {

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

    const EPSILON = 0.001;

    const JUMP_VALUE = 1000;

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

    public $aCovariates;

    public $aRegression;

    public $aCoefficients;


    /**
     * @return mixed
     */
    public function getCoefficients()
    {
        return $this->aCoefficients;
    }

    public function __construct(MathCore $oMath) {
        // внедрли зависимость - мат. ядро
        $this->oMath = $oMath;
    }

    public function setTrainingSet($aTrainingSet)
    {
        $bCheckResult = $this->checkTrainingSet($aTrainingSet);
        if($bCheckResult) {
            $this->separateRegressionAndCovariates($aTrainingSet);
        }
        unset($aTrainingSet);
    }

    /** разделяет регрессоры и значения функции
     * @param $aTrainingSet - массив обучающей выборки
     * @return bool - результат разделения
     */
    private function separateRegressionAndCovariates($aTrainingSet) {
        $aRegression = [];
        $aCovariates = [];
            foreach($aTrainingSet as $key => $value) {
                // регрессия - первый столбец
                $aRegression[] = $value[0];
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

    public function trainModel() {
        $aCoefficients = $this->computeCoefficients();
        if($aCoefficients && count($aCoefficients))
            $this->aCoefficients = $aCoefficients;
    }

    public function estimate() {
        if(isset($this->aCoefficients) && count($this->aCoefficients))
            return $this->constructActualFuncVector($this->aCovariates, $this->aCoefficients);
        return [];
    }

    public function computeCoefficients() {
        $aCoeffVector = $this->oMath->constructVector($this->iCovariatesNum + 1);
        $aFinalCoeffVector = $aCoeffVector;
        $aActualRegVector = $this->constructActualFuncVector($this->aCovariates, $aCoeffVector);
        $fMse = $this->oMath->meanSqError($aActualRegVector, $this->aRegression);
        $iWorseTimes = 0;
        for( $i = 0; $i < self::MAX_NEWTON_RAPHSON_ITERATIONS; ++$i) {
            $aNextCoeffVector = $this->constructNextCoeffVector($aCoeffVector, $this->aCovariates, $this->aRegression, $aActualRegVector);
            if(!$aNextCoeffVector) throw new \Elluminate\Exceptions\SingularException;
            if(!$this->checkChanging($aCoeffVector, $aNextCoeffVector)) return $aFinalCoeffVector;
            if($this->checkJumping($aCoeffVector, $aNextCoeffVector)) return $aFinalCoeffVector;
            $aActualRegVector = $this->constructActualFuncVector($this->aCovariates, $aNextCoeffVector);

            $fNextMse = $this->oMath->meanSqError($aActualRegVector, $this->aRegression);
            if($fNextMse > $fMse) {
                ++$iWorseTimes;
                if($iWorseTimes > self::MAX_WORSE_TIMES)
                    return $aFinalCoeffVector;
//                $aCoeffVector = $aNextCoeffVector;
                for($k = 0; $k < count($aCoeffVector); ++$k)
                    $aCoeffVector[$k] = ( $aCoeffVector[$k] + $aNextCoeffVector[$k] ) / 2;
            }
            else {
                $aCoeffVector = $aNextCoeffVector;
                $aFinalCoeffVector = $aCoeffVector;
                $iWorseTimes = 0;
            }
            $fMse = $fNextMse;
        }
        return $aFinalCoeffVector;
    }

    private function checkChanging($aOldCoeffVector, $aNewCoeffVector) {
        $iRows = count($aOldCoeffVector);
        for($i = 0; $i < $iRows; ++$i)
            if( abs($aOldCoeffVector[$i] - $aNewCoeffVector[$i]) > self::EPSILON )
                return true;
        return false;
    }

    private function checkJumping($aOldCoeffVector, $aNewCoeffVector) {
        $iRows = count($aOldCoeffVector);
        for($i = 0; $i < $iRows; ++$i) {
            if( $aOldCoeffVector[$i] === 0 ) return false;
            if( ( abs($aOldCoeffVector[$i] - $aNewCoeffVector[$i]) / abs($aOldCoeffVector[$i]) ) > self::JUMP_VALUE )
                return true;
        }
        return false;
    }

    private function constructNextCoeffVector($aCoeffVector, $aCovMatrix, $aRegVector, $aActualRegVector) {
        $aTranspCovMatrix = $this->oMath->matrixTranspose($aCovMatrix);
        $a = $this->computeXtilde($aActualRegVector, $aCovMatrix);
        $b = $this->oMath->matrixProduct($aTranspCovMatrix, $a);
        $c = $this->oMath->matrixInverse($b);
        if(!$c) return false;
        $d = $this->oMath->matrixProduct($c, $aTranspCovMatrix);
        $aSubtract = $this->oMath->vectorSubtraction($aRegVector, $aActualRegVector);
        $e = $this->oMath->matrixVectorProduct($d, $aSubtract);
        $aResult = $this->oMath->vectorAddition($aCoeffVector, $e);
        return $aResult;
    }

    private function constructActualFuncVector($aCovMatrix, $aCoeffVector) {
        $aResult = $this->oMath->constructVector($this->iExperimentsNum);
        for($i = 0; $i < $this->iExperimentsNum; ++$i) {
            $z = 0.0;
            for($j = 0; $j< $this->iCovariatesNum + 1; ++$j)
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
    private function checkTrainingSet($aTrainingSet) {
        foreach($aTrainingSet as $row)
            foreach($row as $value)
                //если не заполнено или не является числом
                if(!isset($value) || !is_numeric($value))
                    throw new \Elluminate\Exceptions\TrainSetFileException("Проверьте, что все поля обучающей выборки заполнены и являются числами.");
        // один столбец - значение функции, поэтому регрессоров на 1 меньше
        $this->iCovariatesNum = count($aTrainingSet[0]) - 1;
        $this->iExperimentsNum = count($aTrainingSet);
        // проверяем кол-во регрессоров
        if( $this->iCovariatesNum > self::MAX_COVARIATES_NUM || $this->iCovariatesNum < 1 )
            throw new \Elluminate\Exceptions\TrainSetFileException("Количество параметров регрессии не должно быть больше ".self::MAX_COVARIATES_NUM);
        // проверяем кол-во экспериментов
        if( $this->iExperimentsNum < $this->iCovariatesNum*self::EXPERIMENTS_COVERAGE || $this->iExperimentsNum > self::MAX_EXPERIMENTS_NUM )
            throw new \Elluminate\Exceptions\TrainSetFileException("Количество опытов должно быть не меньше ".$this->iCovariatesNum*self::EXPERIMENTS_COVERAGE." и не больше ".self::MAX_EXPERIMENTS_NUM);
        return true;
    }

    private function computeXtilde($aActualRegVector, $aCovMatrix) {
        $pRows = count($aActualRegVector);
        $xRows = count($aCovMatrix);
        $xCols = count($aCovMatrix[0]);
        if ($pRows != $xRows)
            throw new \Elluminate\Exceptions\DimensionException("Несоответствие размерностей матриц при попытке построить X`");
        $result = $this->oMath->constructMatrix($pRows, $xCols);
        for ($i = 0; $i < $pRows; ++$i)
            for ($j = 0; $j < $xCols; ++$j)
                $result[$i][$j] = $aActualRegVector[$i] * (1 - $aActualRegVector[$i]) * $aCovMatrix[$i][$j];
        return $result;
    }



}