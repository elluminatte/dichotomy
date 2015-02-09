<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 06.01.15
 * Time: 13:50
 */

namespace Elluminate\Math;

/**
 * Class MathCore
 * @package Elluminate\Math
 */
class MathCore {

    public function constructVector($iSize, $uValue = 0) {
        $iSize = (int)$iSize;
        if(!$iSize) return [];
        $aVector = array_fill(0, $iSize, $uValue);
        return $aVector;
    }

    public function constructMatrix($iRowsNum, $iColsNum, $uValue = 0) {
        $iRowsNum = (int)$iRowsNum;
        if(!$iRowsNum) return $this->constructVector($iColsNum);
        $iColsNum = (int)$iColsNum;
        if(!$iColsNum) return $this->constructVector($iRowsNum);
        $aResult = array_fill(0, $iRowsNum, array_fill(0, $iColsNum, $uValue));
        return $aResult;
    }

    public function matrixProduct($aMatrix1, $aMatrix2) {
        $iRows1 = count($aMatrix1); $iCols1 = count($aMatrix1[0]);
        $iRows2 = count($aMatrix2); $iCols2 = count($aMatrix2[0]);
        if ($iCols1 != $iRows2)
            throw new  \Elluminate\Exceptions\DimensionException("Несоответствие размеров матриц при перемножении");
        $aResult = $this->constructMatrix($iRows1, $iCols2);
        for ($i = 0; $i < $iRows1; ++$i) // каждая строка А
            for ($j = 0; $j < $iCols2; ++$j) // каждая колонка В
                for ($k = 0; $k < $iCols1; ++$k)
                    $aResult[$i][$j] += $aMatrix1[$i][$k] * $aMatrix2[$k][$j];
        return $aResult;
    }

    public function matrixVectorProduct($aMatrix, $aVector) {
        $iMRows = count($aMatrix);
        $iMCols = count($aMatrix[0]);
        $iVRows = count($aVector);
        if($iMCols != $iVRows) throw new \Elluminate\Exceptions\DimensionException("Несоответствие размеров матриц при перемножении");
        $aResult = $this->constructVector($iMRows);
        for($i = 0; $i < $iMRows; ++$i)
            for($j = 0; $j < $iMCols; ++$j)
                $aResult[$i] += $aMatrix[$i][$j] * $aVector[$j];
        return $aResult;
    }

    public function matrixTranspose($aMatrix) {
        $result = array();
        $last = sizeof($aMatrix) - 1;
        eval('$result = array_map(null, $aMatrix['
            . implode('], $aMatrix[', range(0, $last)) . ']);');
        return $result;
    }

    public function matrixInverse($aMatrix) {
        $iToggle = 0;
        $aPerm = [];
        $aLum = $this->matrixDecompose($aMatrix, $aPerm, $iToggle);
        $iRows = count($aMatrix);
        if(!$aLum) return false;
        $aB = [];
        $aResult = [];
        for ($i=0; $i<$iRows; ++$i) {
            for ($j=0; $j<$iRows; ++$j) {
                if ($i == $aPerm[$j])
                    $aB[$j] = 1;
                else
                    $aB[$j] = 0;
            }
            $x = $this->equationSolver($aLum, $aB);
            for ($j = 0; $j < $iRows; ++$j)
                $aResult[$j][$i] = $x[$j];
        }
        return $aResult;
    }

    private function matrixDecompose($aMatrix, &$aPerm, &$iToggle) {
        $iRows = count($aMatrix);
        $iCols = count($aMatrix[0]);
        if($iCols != $iRows) throw new \Elluminate\Exceptions\DimensionException("Попытка разложить матрицу, которая не является квадратной");
        for ($i = 0; $i < $iRows; ++$i)
            $aPerm[$i] = $i;
        $iToggle = 1;
        $aResult = $aMatrix;
        for ($j = 0; $j < $iRows - 1; ++$j) {
            $max = abs($aResult[$j][$j]);
            $pRow = $j;
            for ($i = $j + 1; $i < $iRows; ++$i) {
                $aij = abs($aResult[$i][$j]);
                if ($aij > $max) {
                    $max = $aij;
                    $pRow = $i;
                }
            }
            if ($pRow != $j) {
                $rowPtr = $aResult[$pRow];
                $aResult[$pRow] = $aResult[$j];
                $aResult[$j] = $rowPtr;
                $tmp = $aPerm[$pRow];
                $aPerm[$pRow] = $aPerm[$j];
                $aPerm[$j] = $tmp;
                $iToggle = -$iToggle;
            }
            $ajj = $aResult[$j][$j];
            if (abs($ajj) < 0.00000001)
                return false;
            for ($i = $j + 1; $i < $iRows; ++$i) {
                $aij = $aResult[$i][$j] / $ajj;
                $aResult[$i][$j] = $aij;
                for ($k = $j + 1; $k < $iRows; ++$k)
                    $aResult[$i][$k] -= $aij * $aResult[$j][$k];
            }
        }
        return $aResult;
    }

    private function equationSolver($aLuMatrix, $aB) {
        $iRows = count($aLuMatrix);
        // решает Ly = b прямой заменой
        for ($i = 1; $i < $iRows; ++$i) {
            $fSum = $aB[$i];
            for ($j = 0; $j < $i; ++$j)
                $fSum -= $aLuMatrix[$i][$j] * $aB[$j];
            $aB[$i] = $fSum;
        }
        // решает Ux = y обратной заменой
        $aB[$iRows - 1] /= $aLuMatrix[$iRows - 1][$iRows - 1];
        for ($i = $iRows - 2; $i >= 0; --$i)
        {
            $fSum = $aB[$i];
            for ($j = $i + 1; $j < $iRows; ++$j)
                $fSum -= $aLuMatrix[$i][$j] * $aB[$j];
            $aB[$i] = $fSum / $aLuMatrix[$i][$i];
        }
        return $aB;
    }

    public function vectorSubtraction($aVector1, $aVector2) {
        $iRows1 = count($aVector1);
        $iRows2 = count($aVector2);
        if($iRows1 != $iRows2) throw new \Elluminate\Exceptions\DimensionException("Несоответствие размерностей векторов при вычитании");
        $aResult = [];
        for($i = 0; $i < $iRows1; ++$i) {
            $aResult[$i] = $aVector1[$i] - $aVector2[$i];
        }
        return $aResult;
    }

    public function vectorAddition($aVector1, $aVector2) {
        $iRows1 = count($aVector1);
        $iRows2 = count($aVector2);
        if($iRows1 != $iRows2) throw new \Elluminate\Exceptions\DimensionException("Несоответствие размерностей векторов при сложении");
        $aResult = [];
        for($i = 0; $i < $iRows1; ++$i)
            $aResult[$i] = $aVector1[$i] + $aVector2[$i];
        return $aResult;
    }

    public function vectorNumberSubtraction($aVector, $fNumber) {
        if(is_array($aVector) && count($aVector) && is_numeric($fNumber))
            foreach($aVector as &$value)
                $value = $fNumber - $value;
        return $aVector;
    }

    public function getArraySize($aArray) {
        var_dump(max(array_map('count', $aArray)));
        die;
    }

    public static function matrixMean($matrix) {
        return array_sum($matrix)/count($matrix);
    }



    public static function matrixStdDiv($matrix) {
        return sqrt(array_sum(array_map("self::sdSquare", $matrix, array_fill(0,count($matrix), (array_sum($matrix) / count($matrix)) ) ) ) / (count($matrix)-1) );
    }

    public static function matrixDiff($matrix1, $matrix2) {
        $result = array();
        foreach($matrix1 as $key => $value) {
            $result[$key] = abs($value - $matrix2[$key]);
        }
        return $result;
    }

    public static function findVectorMinimum($matrix) {
        $result = array_keys($matrix, min($matrix));
        //может быть несколько одинаковых чисел, поэтому берем первое попавшееся
        $last = sizeof($result) - 1;
        return $result[$last];
    }



    public static function meanSqError($matrix1, $matrix2) {
        $pRows = count($matrix1);
        $yRows = count($matrix2);
        if( $pRows != $yRows) throw new \Elluminate\Exceptions\DimensionException("Несоответствие размерностей матриц при попытке найти СКО");
        if ($pRows == 0)
            return 0;
        $result = 0;
        for ($i=0; $i<$pRows; ++$i)
            $result += ($matrix1[$i] - $matrix2[$i]) * ($matrix1[$i] - $matrix2[$i]);
        return $result/$pRows;
    }

    public static function matrixAddition($matrix1, $matrix2) {

    }

    public static function matrixSubtraction($matrix1, $matrix2) {

    }



    private static function sdSquare($x, $mean) {
        return pow($x - $mean,2);
    }

    public function computeXtilde($pVector, $xMatrix) {
        $pRows = count($pVector);
        $xRows = count($xMatrix);
        $xCols = count($xMatrix[0]);
        if ($pRows != $xRows)
            throw new \Elluminate\Exceptions\DimensionException("Несоответствие размерностей матриц при попытке построить X`");
        $result = $this->constructMatrix($pRows, $xCols);
        for ($i = 0; $i < $pRows; ++$i)
            for ($j = 0; $j < $xCols; ++$j)
                $result[$i][$j] = $pVector[$i] * (1 - $pVector[$i]) * $xMatrix[$i][$j];
        return $result;
    }



}