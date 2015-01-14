<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 06.01.15
 * Time: 13:45
 */

namespace Elluminate\Math;

/**
 * Class RocAnalysis
 * @package Elluminate\Math
 */
class RocAnalysis {

    /**
     * @var - модель, для которой необходимо провести ROC-анализ
     */
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

}