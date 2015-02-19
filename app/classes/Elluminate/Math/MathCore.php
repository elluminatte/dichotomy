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
class MathCore
{

    /** создает вектор
     * @param $iSize - размер
     * @param int $uValue - значение для заполнения
     * @return array - вектор
     */
    public function constructVector($iSize, $uValue = 0)
    {
        $iSize = (int)$iSize;
        if (!$iSize) return [];
        $aVector = array_fill(0, $iSize, $uValue);
        return $aVector;
    }

    /** создает матрицу
     * @param $iRowsNum - число строк
     * @param $iColsNum - число столбцов
     * @param int $uValue - значение для заполнения
     * @return array - матрица
     */
    public function constructMatrix($iRowsNum, $iColsNum, $uValue = 0)
    {
        $iRowsNum = (int)$iRowsNum;
        if (!$iRowsNum) return $this->constructVector($iColsNum);
        $iColsNum = (int)$iColsNum;
        if (!$iColsNum) return $this->constructVector($iRowsNum);
        $aResult = array_fill(0, $iRowsNum, array_fill(0, $iColsNum, $uValue));
        return $aResult;
    }

    /** считает произведение матриц
     * @param $aMatrix1 - матрица 1
     * @param $aMatrix2 - матрица 2
     * @return array - результат произведения матриц
     * @throws \Elluminate\Exceptions\MathException
     */
    public function matrixProduct($aMatrix1, $aMatrix2)
    {
        $iRows1 = count($aMatrix1);
        $iCols1 = count($aMatrix1[0]);
        $iRows2 = count($aMatrix2);
        $iCols2 = count($aMatrix2[0]);
        if ($iCols1 != $iRows2)
            throw new  \Elluminate\Exceptions\MathException("Несоответствие размеров матриц при перемножении");
        $aResult = $this->constructMatrix($iRows1, $iCols2);
        for ($i = 0; $i < $iRows1; ++$i) // каждая строка А
            for ($j = 0; $j < $iCols2; ++$j) // каждая колонка В
                for ($k = 0; $k < $iCols1; ++$k)
                    $aResult[$i][$j] += $aMatrix1[$i][$k] * $aMatrix2[$k][$j];
        return $aResult;
    }

    /** считает произведение матрицы на вектор
     * @param $aMatrix - матрица
     * @param $aVector - вектор
     * @return array - результат произведения
     * @throws \Elluminate\Exceptions\MathException
     */
    public function matrixVectorProduct($aMatrix, $aVector)
    {
        $iMRows = count($aMatrix);
        $iMCols = count($aMatrix[0]);
        $iVRows = count($aVector);
        if ($iMCols != $iVRows) throw new \Elluminate\Exceptions\MathException("Несоответствие размеров матриц при перемножении");
        $aResult = $this->constructVector($iMRows);
        for ($i = 0; $i < $iMRows; ++$i)
            for ($j = 0; $j < $iMCols; ++$j)
                $aResult[$i] += $aMatrix[$i][$j] * $aVector[$j];
        return $aResult;
    }

    /** транспонирует матрицу
     * @param $aMatrix - входная матрица
     * @return array - результат транспонирования
     * @throws \Elluminate\Exceptions\MathException
     */
    public function matrixTranspose($aMatrix)
    {
        if (!is_array($aMatrix) || !count($aMatrix)) throw new \Elluminate\Exceptions\MathException("Ошибка при транспонировании матрицы");
        $result = array();
        $last = sizeof($aMatrix) - 1;
        eval('$result = array_map(null, $aMatrix['
            . implode('], $aMatrix[', range(0, $last)) . ']);');
        return $result;
    }

    /** ищет обратную матрицу
     * @param $aMatrix - входгная матрица
     * @return array|bool - обратная матрица или false, если найти не получается
     * @throws \Elluminate\Exceptions\MathException
     */
    public function matrixInverse($aMatrix)
    {
        if (!is_array($aMatrix) || !count($aMatrix)) throw new \Elluminate\Exceptions\MathException("Ошибка при поиске обратной матрицы");
        $iToggle = 0;
        $aPerm = [];
        $aLum = $this->matrixDecompose($aMatrix, $aPerm, $iToggle);
        $iRows = count($aMatrix);
        if (!$aLum) return false;
        $aB = [];
        $aResult = [];
        for ($i = 0; $i < $iRows; ++$i) {
            for ($j = 0; $j < $iRows; ++$j) {
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

    /** раскладывает матрицу
     * @param $aMatrix - входная матрица
     * @param $aPerm
     * @param $iToggle
     * @return bool - разложенная матрица или false, если разложить не получилось
     * @throws \Elluminate\Exceptions\MathException
     */
    private function matrixDecompose($aMatrix, &$aPerm, &$iToggle)
    {
        $iRows = count($aMatrix);
        $iCols = count($aMatrix[0]);
        if ($iCols != $iRows) throw new \Elluminate\Exceptions\MathException("Попытка разложить матрицу, которая не является квадратной");
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

    /** решает уравнение Ax = B
     * @param $aLuMatrix - разложенная матрица
     * @param $aB - матрица B
     * @return mixed - найденная матрица B
     * @throws \Elluminate\Exceptions\MathException
     */
    private function equationSolver($aLuMatrix, $aB)
    {
        if (!is_array($aLuMatrix) || !count($aLuMatrix)) throw new \Elluminate\Exceptions\MathException("Ошибка при решении уравнения Ax=b");
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
        for ($i = $iRows - 2; $i >= 0; --$i) {
            $fSum = $aB[$i];
            for ($j = $i + 1; $j < $iRows; ++$j)
                $fSum -= $aLuMatrix[$i][$j] * $aB[$j];
            $aB[$i] = $fSum / $aLuMatrix[$i][$i];
        }
        return $aB;
    }

    /** считает разность векторов
     * @param $aVector1 - вектор 1
     * @param $aVector2 - вектор 2
     * @return array - результат разности
     * @throws \Elluminate\Exceptions\MathException
     */
    public function vectorSubtraction($aVector1, $aVector2)
    {
        $iRows1 = count($aVector1);
        $iRows2 = count($aVector2);
        if ($iRows1 != $iRows2) throw new \Elluminate\Exceptions\MathException("Несоответствие размерностей векторов при вычитании");
        $aResult = [];
        for ($i = 0; $i < $iRows1; ++$i) {
            $aResult[$i] = $aVector1[$i] - $aVector2[$i];
        }
        return $aResult;
    }

    /** считает сумму векторов
     * @param $aVector1 - вектор 1
     * @param $aVector2 - вектор 2
     * @return array - результат суммирования
     * @throws \Elluminate\Exceptions\MathException
     */
    public function vectorAddition($aVector1, $aVector2)
    {
        $iRows1 = count($aVector1);
        $iRows2 = count($aVector2);
        if ($iRows1 != $iRows2) throw new \Elluminate\Exceptions\MathException("Несоответствие размерностей векторов при сложении");
        $aResult = [];
        for ($i = 0; $i < $iRows1; ++$i)
            $aResult[$i] = $aVector1[$i] + $aVector2[$i];
        return $aResult;
    }

    /** считает разность числа и вектора
     * @param $aVector - вектор
     * @param $fNumber - число
     * @return mixed - результирующий вектор
     */
    public function vectorNumberSubtraction($aVector, $fNumber)
    {
        if (is_array($aVector) && count($aVector) && is_numeric($fNumber)) {
            foreach ($aVector as &$value)
                $value = $fNumber - $value;
            return $aVector;
        } else throw new \Elluminate\Exceptions\MathException("Ошибка при поиске разности числа и вектора");
    }

    /** считает мат. ожидание матрицы
     * @param $matrix - матрица
     * @return float - мат. ожидание
     * @throws \Elluminate\Exceptions\MathException
     */
    public static function matrixMean($matrix)
    {
        if (is_array($matrix) && count($matrix))
            return array_sum($matrix) / count($matrix);
        else throw new \Elluminate\Exceptions\MathException("Ошибка поиска математического ожидания в матрице");
    }


    /** считает СКО матрицы
     * @param $matrix - матрица
     * @return float - СКО
     * @throws \Elluminate\Exceptions\MathException
     */
    public static function matrixStdDiv($matrix)
    {
        if (is_array($matrix) && count($matrix))
            return sqrt(array_sum(array_map("self::sdSquare", $matrix, array_fill(0, count($matrix), (array_sum($matrix) / count($matrix))))) / (count($matrix) - 1));
        else throw new \Elluminate\Exceptions\MathException("Ошибка поиска СКО в матрице");
    }

    /** считает разность матриц
     * @param $matrix1 - матрица 1
     * @param $matrix2 - матрица 2
     * @return array - результат разности
     * @throws \Elluminate\Exceptions\MathException
     */
    public static function matrixDiff($matrix1, $matrix2)
    {
        if (!is_array($matrix1) || !is_array($matrix2) || count($matrix1) != count($matrix2)) throw new \Elluminate\Exceptions\MathException("Ошибка поиска разности матриц");
        $result = array();
        foreach ($matrix1 as $key => $value) {
            $result[$key] = abs($value - $matrix2[$key]);
        }
        return $result;
    }

    /** ищет минимум вектора
     * @param $matrix - вектор
     * @return mixed - минимальное значение
     * @throws \Elluminate\Exceptions\MathException
     */
    public static function findVectorMinimum($matrix)
    {
        if (!is_array($matrix) || !count($matrix)) throw new \Elluminate\Exceptions\MathException("Ошибка поиска минимума в матрице");
        $result = array_keys($matrix, min($matrix));
        //может быть несколько одинаковых чисел, поэтому берем первое попавшееся
        $last = sizeof($result) - 1;
        return $result[$last];
    }

    /** считает СКО между двумя матрицами
     * @param $matrix1 - матрица 1
     * @param $matrix2 - матрица 2
     * @return float|int - СКО
     * @throws \Elluminate\Exceptions\MathException
     */
    public static function meanSqError($matrix1, $matrix2)
    {
        $pRows = count($matrix1);
        $yRows = count($matrix2);
        if ($pRows != $yRows) throw new \Elluminate\Exceptions\MathException("Несоответствие размерностей матриц при попытке найти СКО");
        if ($pRows == 0)
            return 0;
        $result = 0;
        for ($i = 0; $i < $pRows; ++$i)
            $result += ($matrix1[$i] - $matrix2[$i]) * ($matrix1[$i] - $matrix2[$i]);
        return $result / $pRows;
    }


    /** считает квадрат разности, нужна для поиска СКО
     * @param $x
     * @param $mean
     * @return number
     */
    private static function sdSquare($x, $mean)
    {
        return pow($x - $mean, 2);
    }

    public static function logisticRegression($aCovariates, $aCoefficients) {
        if(!is_array($aCovariates) || !is_array($aCoefficients) || !count($aCovariates) || !count($aCoefficients)) throw new \Elluminate\Exceptions\MathException("Ошибка при расчете значения логистической регрессии");
        if(count($aCovariates) != count($aCoefficients)-1) throw new \Elluminate\Exceptions\MathException("Количество коэффициентов не соответствует количеству регрессоров");
        $z = 0;
        array_unshift($aCovariates, 1);
        for ($j = 0; $j < count($aCovariates); ++$j)
            $z += $aCovariates[$j] * $aCoefficients[$j];
        $fResult = 1 / (1 + exp(-$z));
        return $fResult;
    }


}