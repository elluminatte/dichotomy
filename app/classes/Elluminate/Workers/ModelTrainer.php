<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 19.02.15
 * Time: 16:53
 */
namespace Elluminate\Workers;
class ModelTrainer {
    public function fire($job, $data) {
        $iUserId = (int)$data['user_id'];
        if(!$iUserId || !\User::find($iUserId)) $job->delete();
        $iModelId = (int)$data['user_id'];
        if(!$iModelId || !\Model::find($iModelId)) $job->delete();
        \Evaluation::where('user_id', '=', $iUserId)->where('model_id', '=', $iModelId)->delete();
        $aCovariates = $data['covariates'];
        $aRegression = $data['regression'];
        $job->delete();
    }
}