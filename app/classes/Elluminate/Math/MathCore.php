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

    public static function matrixMean($matrix) {
        return array_sum($matrix)/count($matrix);
    }

    public static function matrixTranspose($matrix) {
        $result = array();
        $last = sizeof($matrix) - 1;
        eval('$result = array_map(null, $matrix['
            . implode('], $matrix[', range(0, $last)) . ']);');
        return $result;
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

    public static function matrixMin($matrix) {
        $result = array_keys($matrix, min($matrix));
        //может быть несколько одинаковых чисел, поэтому берем первое попавшееся
        $last = sizeof($result) - 1;
        return $result[$last];
    }

    public static function matrixProduct($matrix1, $matrix2) {

    }

    public static function meanSqError($matrix1, $matrix2) {
        $pRows = count($matrix1);
        $yRows = count($matrix2);
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

    public static function matrixInverse($matrix) {

    }

    private static function sdSquare($x, $mean) {
        return pow($x - $mean,2);
    }


}