<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 19.02.15
 * Time: 16:53
 */
namespace Elluminate\Workers;

class ModelTrainer {

    protected $oModelRepo;

    public function __construct(\ClientModelRepository $oModelRepo) {
        $this->oModelRepo = $oModelRepo;
    }

    public function fire($job, $data) {
        $iEvaluationId = (int)$data['iEvaluationId'];
        // если нет такого вычисления, то просто удалим это задание
        if(!$iEvaluationId || !\Evaluation::find($iEvaluationId, ['id'])) $job->delete();
        $oEvaluation = \Evaluation::find($iEvaluationId);
        // получим реальный результат и введенные пользователем параметры из БД
        $iRealResult = $oEvaluation->real_result;
        // восстановим массив параметров
        $aCovariates = json_decode($oEvaluation->covariates);
        $aRow = $aCovariates;
        unset($aCovariates);
        // получается строка {Значение функции}{Регрессор1}...{РегрессорN} - в общем как в обучающей выборке
        array_unshift($aRow, $iRealResult);
        unset($iRealResult);
        $iModelId = $oEvaluation->model_id;
        $oModel = \Model::find($iModelId);
        // восстановим массив доп. выборки, или если там пусто, то будем писать с нуля
        $aOversampling = is_array(json_decode($oModel->oversampling)) ? json_decode($oModel->oversampling) : array();
        // допишем в доп. выборку строчку из нашего вычисления
        array_push($aOversampling, $aRow);
        unset($aRow);
        $oModel->oversampling = json_encode($aOversampling);
        $oModel->save();
        // переобучим модель
        $this->oModelRepo->retrainModel($iModelId);
        // теперь удаляем сущность вычисления, она больше не пригодится
        $oEvaluation->delete();
        // удаляем задачу из очереди
        $job->delete();
    }
}