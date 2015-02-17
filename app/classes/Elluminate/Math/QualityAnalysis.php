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
class QualityAnalysis {

    /**
     * @var - модель, качество которой необходимо оценить
     */
    private $oModel;

    private $oMath;

    private $aSpecificity;

    private $aSensitivity;

    private $aElasticCoeff;

    private $aStdCoeff;

    private $fSill;

    private $fArea;

    public function __construct(MathCore $oMath) {
        if($oMath instanceof \Elluminate\Math\MathCore)
            $this->oMath = $oMath;
        else throw new \Elluminate\Exceptions\InstanceException("Предоставляемые математические инструменты должны быть математическим ядром");
    }

    public function setModel($oModel) {
        if($oModel instanceof \Elluminate\Math\LogisticRegression)
            $this->oModel = $oModel;
        else throw new \Elluminate\Exceptions\InstanceException("Модель, качество которой анализируется, должна быть логистической регрессией");
    }

    public function getQualityAnalysis() {
        $this->calcSpecAndSens();
        $this->fSill = $this->findSill();
        $this->fArea = $this->calcAreaUnderRocCurve();
        $this->findElasticAndStdCoeff();
        unset($this->aSensitivity); unset($this->aSpecificity); unset($this->oModel); unset($this->oMath);
    }

    private function calcSpecAndSens() {
        $iStartPoint = 0;
        $fStep = 0.01;
        $iEndPoint = 1;
        $aOutcomes = $this->countOutcomes();
        $iPositives = isset($aOutcomes['positives']) ? $aOutcomes['positives'] : 0;
        $iNegatives = isset($aOutcomes['negatives']) ? $aOutcomes['negatives'] : 0;
        $fRoller = $iStartPoint;
        $aEstimatedFunction = $this->oModel->estimate();
        $j = 0;
        while($fRoller <= $iEndPoint) {
            $iFalsePositive = $iTruePositive =  0;
            if(is_array($aEstimatedFunction) && count($aEstimatedFunction))
                foreach($aEstimatedFunction as $key => $value) {
                    if($value >= $fRoller) {
                        if (isset($this->oModel->aRegression[$key]) && $this->oModel->aRegression[$key] == 1)
                            $iTruePositive++;
                        else
                            $iFalsePositive++;
                    }

                }
            if($iPositives)
                $this->aSensitivity[$j] = $iTruePositive/$iPositives*100;
            if($iNegatives)
                $this->aSpecificity[$j] = $iFalsePositive/$iNegatives*100;
            $j++;
            $fRoller += $fStep;
        }
        return true;
    }

    private function findSill() {
        $aDifference = $this->oMath->matrixDiff($this->oMath->vectorNumberSubtraction($this->aSpecificity, 100), $this->aSensitivity);
        $iSill = $this->oMath->findVectorMinimum($aDifference);
        unset($aDifference);
        return $iSill;
    }

    private function countOutcomes() {
        $iNegative = 0;
        $iPositive = 0;
        if(is_array($this->oModel->aRegression) && count($this->oModel->aRegression))
            foreach( $this->oModel->aRegression as $value) {
                if($value == 1)
                    $iPositive++;
                if($value == 0)
                    $iNegative++;
            }
        return ['positives' => $iPositive, 'negatives' => $iNegative];
    }

    private function findElasticAndStdCoeff() {
        $aCoefficients = array_slice($this->oModel->aCoefficients, 1);
        $aTranspCov = $this->oMath->matrixTranspose($this->oModel->aCovariates);
        $aTranspCov = array_slice($aTranspCov, 1);
        $i = 0;
        $aElasticCoeff = $aStdCoeff = [];
        if(!is_array($aTranspCov) || !count($aTranspCov))
            throw new \Elluminate\Exceptions\DimensionException("Ошибка при попытке найти мат. ожидание и СКО для регрессоров");
        $aRegMean = $this->oMath->matrixMean($this->oModel->aRegression);
        $aRegStd = $this->oMath->matrixStdDiv($this->oModel->aRegression);
        foreach($aTranspCov as $value) {
            $aCovMean = $this->oMath->matrixMean($value);
            $aCovStd = $this->oMath->matrixStdDiv($value);
            $aElasticCoeff[$i] = $aCoefficients[$i]*$aCovMean/$aRegMean;
            $aStdCoeff[$i] = $aCoefficients[$i]*$aCovStd/$aRegStd;
            $i++;
        }
        unset($aCoefficients); unset($aTranspCov);
        if(is_array($aElasticCoeff) && count($aElasticCoeff))
            $this->aElasticCoeff = $aElasticCoeff;
        if(is_array($aStdCoeff) && count($aStdCoeff))
            $this->aStdCoeff = $aStdCoeff;
    }

    private function calcAreaUnderRocCurve() {
        $iSensitivityCount = count($this->aSensitivity);
        $iSpecificityCount = count($this->aSpecificity);
        if( ($iSensitivityCount != $iSpecificityCount) || !$iSensitivityCount)
            throw new \Elluminate\Exceptions\DimensionException("Ошибка при попытке посчитать площадь под ROC-кривой");
        $i = 0;
        $fSum = 0;
        while( $i <= $iSensitivityCount - 2 ) {
            $fSum += abs($this->aSpecificity[$i+1] - $this->aSpecificity[$i]) * (( $this->aSensitivity[$i+1] + $this->aSensitivity[$i]) / 2);
            $i++;
        }
        $fArea = round($fSum/100, 2);
        return $fArea;
    }

}