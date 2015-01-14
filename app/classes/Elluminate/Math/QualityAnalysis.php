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
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

}