<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 28.12.14
 * Time: 1:07
 */

namespace Elluminate\Math;


/**
 * Class LogisticRegression
 * @package Elluminate\Math
 */
class LogisticRegression {

    /**
     * @var - массив с обучающим примером
     */
    private $trainData;

    /**
     * @var - массив регрессоров
     */
    private $covariates;

    /**
     * @var - массив с наименованиями переменных, функции и регрессоров
     */
    private $names;

    /**
     * @var - массив (вектор) значений функции
     */
    private $regression;

    /**
     * @var - массив (вектор) коэффициентов логистической регрессии
     */
    private $coefficients;

    /**
     * @var int - максимальное кол-во итераций при поиске коэффициентов методом Ньютона-Рафсона
     */
    private $maxIterations;

    /**
     * @var float - точность поиска коэффициентов
     */
    private $epsilon;

    /**
     * @var int - размер прыжка метода Ньютона-Рафсона, при котором можно считать, что метод не сошелся
     */
    private $jumpFactor;

    /**
     * @var - массив с логом процесса работы класса
     * ToDo: может сделать коды ситуаций, и если ситуации наступают, то обучение прошло неправильно
     */
    public $log;

    public function __construct($trainData, $maxIterations = 1000, $epsilon = 0.001, $jumpFactor = 1000) {
        $log = array();
        $this->trainData = $trainData;
        $this->maxIterations = $maxIterations;
        $this->epsilon = $epsilon;
        $this->jumpFactor = $jumpFactor;
        unlink($trainData);
    }

    public function estimateCoefficients() {
        $this->separateCovRegNames();
        $this->coefficients = $this->computeBettaVector();
    }

    /**
     * анализирует данные, полученные из файла обучающнй выборки
     * и выделяет из них массив регрессоров, вектор значений целевой функции
     * и вектор наименований регрессоров и функции
     */
    private function separateCovRegNames() {
        $this->covariates = $this->separateCovariates();
        $this->regression = $this->separateRegression();
        $this->names = $this->separateNames();
    }

    private function computeBettaVector() {

    }

    private function separateCovariates() {
        $covariates = array();
        return $covariates;
    }

    private function separateRegression() {
        $regression = array();
        return $regression;
    }

    private function separateNames() {
        $names = array();
        return $names;
    }



}