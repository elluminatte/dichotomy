<?php

/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 23.02.15
 * Time: 15:44
 */
class EvaluationRepository
{

    /**
     * @var ClientModelRepository - клиентский репозиторий моделей (решаемых задач)
     */
    protected $oModelRepo;

    public function __construct(ClientModelRepository $oModelRepo)
    {
        $this->oModelRepo = $oModelRepo;
    }

    /** добавляет факт совершения вычислений пользователем
     * @param $iModelId - id модели
     * @param $aCovariates - введенные пользователем значения параметров
     * @param $fEstimatedResult - просчитанный моделью результат
     * @throws \Elluminate\Exceptions\EvaluationException
     */
    public function addEvaluation($iModelId, $aCovariates, $fEstimatedResult)
    {
        // получим пользователя и проверим есть ли такой
        $iUserId = (int)\Auth::user()->id;
        if (!$iUserId || !User::find($iUserId, ['id'])) throw new \Elluminate\Exceptions\EvaluationException("Не представлен идентификатор пользователя");
        // то же и с моделью
        $iModelId = (int)$iModelId;
        if (!$iModelId || !Model::find($iModelId, ['id'])) throw new \Elluminate\Exceptions\EvaluationException("Не представлен идентификатор модели");
        if (!is_array($aCovariates) || !count($aCovariates)) throw new \Elluminate\Exceptions\EvaluationException("Некорректный массив регрессоров");
        if ($fEstimatedResult > 1 || $fEstimatedResult < 0) throw new \Elluminate\Exceptions\EvaluationException("Результат вычисления логистической регрессии выходит за диапазон допустимых значений 0-1");
        $oModel = Model::with('duration')->find($iModelId, ['id', 'durations_id', 'sill']);
        $iDuration = (int)$oModel->duration->duration;
        $fSill = $oModel->sill;
        unset($oModel);
        $oEvaluation = new Evaluation();
        // граница между Да и Нет находится не в 0,5, а в абсциссе порога отсечения
        $oEvaluation->estimated_result = $fEstimatedResult <= $fSill ? 0 : 1;
        $oEvaluation->covariates = json_encode($aCovariates, JSON_NUMERIC_CHECK);
        // добавим к текущему времени интервал корректности решения
        $oEvaluation->expired_moment = \Carbon\Carbon::now()->addHours($iDuration);
        $oEvaluation->user_id = $iUserId;
        $oEvaluation->model_id = $iModelId;
        // сохраним новую сущность
        $oEvaluation->save();
    }

    /**
     * получает уведомления об ожидающих и просроченных задачах
     */
    public static function getNotifications()
    {
        // просроченные задачи
        self::checkExpiredEvaluations();
        // ожидающие задачи
        self::checkWaitingEvaluations();
    }

    /**
     * проверяет пользователя на просроченные задачи
     */
    public static function checkExpiredEvaluations()
    {
        // сбросим предыдущий результат проверки
        Session::forget('expired_evaluations');
        $iUserId = \Auth::user()->id;
        // найдем соответсвующие текущему пользователю вычисления с незаполенным реальным результатом и прошедшей датой и временем
        if (!$iUserId || !User::find($iUserId, ['id'])) return;
        $oEvaluations = Evaluation::where('user_id', '=', $iUserId)->whereNull('real_result')->where('expired_moment', '<', 'NOW()')->get();
        // если такие сущности нашлись, то запишем в сессию флажок
        if (!$oEvaluations->isEmpty()) {
            Session::flash('expired_evaluations', 1);
            return true;
        }
        return false;
    }

    /**
     * проверяет пользователя на ожидающие вычисления
     */
    public static function checkWaitingEvaluations()
    {
        // если уже есть просроченные, то про ожидающие можно и не говорить
        if (Session::get('expired_evaluations') == 1) return;
        // сбросим результат прошлой проверки
        Session::forget('waiting_evaluations');
        $iUserId = \Auth::user()->id;
        if (!$iUserId || !User::find($iUserId, ['id'])) return;
        // найдем для этого пользователя сущности с незаполненным реальным результатмом
        $oEvaluations = Evaluation::where('user_id', '=', $iUserId)->whereNull('real_result')->get();
        // если такие есть, то запишем флажок в сессию
        if (!$oEvaluations->isEmpty()) {
            Session::flash('waiting_evaluations', 1);
            return true;
        }
        return false;
    }

    /** собирает точно такую же форму, как заполнил ее пользователь
     * @param $iEvaluationId - id вычисления
     * @return array - массив полей формы
     */
    public function getConfirmationForm($iEvaluationId)
    {
        $iEvaluationId = (int)$iEvaluationId;
        if (!$iEvaluationId || !Evaluation::where('id', '=', $iEvaluationId)->where('user_id', '=', \Auth::user()->id)) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oEvaluation = Evaluation::find($iEvaluationId);
        $iModelId = (int)$oEvaluation->model_id;
        // восстановим массив значений, которые пользователь вводил, из БД
        $aValues = json_decode($oEvaluation->covariates);
        // соберем форму как и при решении задачи, только подставим туда прошлые значения
        $oForm = $this->oModelRepo->getApplyingForm($iModelId, $aValues);
        return $oForm;
    }

    /** получает просчитанный моделью результат в случае конкретного вычисления
     * @param $iEvaluationId - id вычисления
     * @return mixed - результат
     */
    public function getEstimatedResult($iEvaluationId)
    {
        $iEvaluationId = (int)$iEvaluationId;
        // получим сущность, где пользователь равен текущему и id равен требуемому
        if (!$iEvaluationId || !Evaluation::where('id', '=', $iEvaluationId)->where('user_id', '=', \Auth::user()->id)) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oEvaluation = Evaluation::find($iEvaluationId);
        // если нашлось, то вернем результат
        if (!empty($oEvaluation)) return $oEvaluation->estimated_result;
        else throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    }

    /** завершает обратную связь
     * @param $iEvaluationId - id вычисления
     * @param $iRealResult - реальный результат
     * @return bool|null
     */
    public function confirm($iEvaluationId, $iRealResult)
    {
        $iEvaluationId = (int)$iEvaluationId;
        if (!$iEvaluationId || !Evaluation::where('id', '=', $iEvaluationId)->where('user_id', '=', \Auth::user()->id)) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oEvaluation = Evaluation::find($iEvaluationId);
        $iRealResult = (int)$iRealResult;
        // -1 это когда у пользователя нет данных, в таком случае просто удалим это вычисление, пользы от него немного
        // иначе
        if ($iRealResult != -1) {
            // запишем реальный результат в БД
            $oEvaluation->real_result = $iRealResult;
            $bResult = $oEvaluation->save();
            unset($oEvaluation);
            // поставим в очередь задание на переобучение модели
            Queue::push('\Elluminate\Workers\ModelTrainer', ['iEvaluationId' => $iEvaluationId]);
        } else
            $bResult = $oEvaluation->delete();
        // надо обновить уведомления о ждущих и просроченных задачах, может таких уже и нет
        self::getNotifications();
        return $bResult;
    }
}