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

    public function __construct(LogisticRegression $oModel, MathCore $oMath) {
        $this->oModel = $oModel;
        $this->oMath = $oMath;
    }

    private function calcSpecAndSens() {
        $iStartPoint = 0;
        $fStep = 0.1;
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
                        if (isset($this->oModel->aRegression[$key]) && $this->oModel->aRegression[$key] === 1)
                            $iTruePositive++;
                        else
                            $iFalsePositive++;
                    }

                }
            $this->aSensitivity[$j] = $iTruePositive/$iPositives*100;
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
        foreach( $this->oModel->aRegression as $value) {
            if($value === 1)
                $iPositive++;
            if($value === 0)
                $iNegative++;
        }
        return ['positives' => $iPositive, 'negatives' => $iNegative];
    }

    private function findElasticCoefficients() {
        $aCoefficients = array_slice($this->oModel->aCoefficients, 1);
        foreach($this->oModel->aCovariates as $value) {

        }
    }

}