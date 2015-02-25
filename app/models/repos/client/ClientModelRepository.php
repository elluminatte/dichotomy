<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 25.02.15
 * Time: 12:13
 */
class ClientModelRepository extends ModelRepository {

    /** собирает форму для ввода параметров
     * @param $iModelId - id модели
     * @param array $aValues - значения, которыми нужно заполнить поля
     * @return array - массив с полями формы
     */
    public function getApplyingForm($iModelId, $aValues = []) {
        $iModelId = (int)$iModelId;
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // какие поля надо взять
        $aFields = ['id', 'cov_names', 'cov_comments'];
        $oModel = $this->getModel($iModelId, $aFields);
        // восстанавливаем массив из БД
        $aNames = json_decode($oModel->cov_names);
        $aComments = json_decode($oModel->cov_comments);
        $aForm = [];
        // надо ли заполнять поля значениями
        $bNeedValues = false;
        if(is_array($aValues) && count($aValues))
            $bNeedValues = true;
        foreach($aNames as $key => $value) {
            $sName = $value;
            $sComment = isset($aComments[$key]) ? $aComments[$key] : '';
            if($bNeedValues)
                $sValue = isset($aValues[$key]) ? $aValues[$key] : '';
            // вставляем очередное поле в массив формы
            if(isset($sValue))
                array_push($aForm, ['tech_name' => \Elluminate\Engine\E::transliterate($sName), 'name' => $sName, 'comment' => $sComment, 'value' => $sValue]);
            else
                array_push($aForm, ['tech_name' => \Elluminate\Engine\E::transliterate($sName), 'name' => $sName, 'comment' => $sComment]);
        }
        unset($aNames);
        unset($aComments);
        return $aForm;
    }

    /** валидирует пользовательский ввод в форму
     * @param $aInput - массив входных данных
     * @return \Illuminate\Validation\Validator - экземпляр валидатора
     */
    public function validateUserInput($aInput) {
        // проверим есть ли вообще такая модель
        $iModelId = (int)$aInput['model_id'];
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // соберем форму
        $oForm = $this->getApplyingForm($iModelId);
        // получим правила валидации и названия полей для вывода сообщений
        $aRulesAndNames = $this->getUserValidRulesAndNames($oForm);
        $aValidRules = $aRulesAndNames['rules'];
        $aFieldNames = $aRulesAndNames['names'];
        return \Validator::make($aInput, $aValidRules, array(), $aFieldNames);
    }

    /** формирует правила валидации и названия полей для вывода сообщений
     * @param $oForm - массив формы
     * @return array - правила и названия
     */
    private function getUserValidRulesAndNames($oForm) {
        $aValidRules = [];
        $aNames = [];
        foreach($oForm as $aField) {
            // все поля должны быть числовыми и обязательными для заполнения
            $aValidRules[$aField['tech_name']] = 'Required|Numeric';
            $aNames[$aField['tech_name']] = $aField['name'];
        }
        return ['names' => $aNames, 'rules' => $aValidRules];
    }

    /** считает значение логистической регрессии при введенных пользователем параметрах
     * @param $aInput - входные данные
     * @return float - значение логистической регрессии
     * @throws \Elluminate\Exceptions\EvaluationException
     * @throws \Elluminate\Exceptions\MathException
     */
    public function computeResult($aInput) {
        $iModelId = $aInput['model_id'];
        $iModelId = (int)$iModelId;
        // проверим есть ли нужная нам модель
        if(!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // уберем поля, которые не являются значениями регрессоров
        unset($aInput['model_id']);
        unset($aInput['_token']);
        $aCovValues = [];
        foreach($aInput as $value) {
            array_push($aCovValues, $value);
        }
        $aFields = ['id', 'coefficients'];
        $oModel = $this->getModel($iModelId, $aFields);
        // восстановим массив коэффициентов из БД
        $aCoefficients = json_decode($oModel->coefficients);
        // посчитаем логит. регрессию
        $fResult = round(\Elluminate\Math\MathCore::logisticRegression($aCovValues, $aCoefficients), 2);
        // получим id пользователя, который всё это считает
        $oEvaluationRepo = new EvaluationRepository($this);
        // добавим факт использования модели в БД
        $oEvaluationRepo->addEvaluation($iModelId, $aCovValues, $fResult);
        return $fResult;
    }
    /** переобучает модель с учетом дополнительной выборки
     * @param $iModelId - id модели, которую надо переобучить
     * @throws \Elluminate\Exceptions\InstanceException
     */
    public function retrainModel($iModelId) {
        $iModelId = (int)$iModelId;
        // проверим есть ли такая модель вообще
        if (!$iModelId || !Model::find($iModelId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        $oModel = Model::find($iModelId);
        // объединим основную и дополнительную обучающую выборку
        $aCoreSelection = json_decode($oModel->core_selection);
        $aOversampling = json_decode($oModel->oversampling);
        if(!is_array($aCoreSelection) || !is_array($aOversampling) || (count($aCoreSelection[0]) != count($aOversampling[0]))) throw new \Elluminate\Exceptions\EvaluationException("Несоответствие размерностей основной и обучающей выборки");
        $aTrainingSet = array_merge($aCoreSelection, $aOversampling);
        // установим ее как единую выборку для обучения
        $this->oModel->setTrainingSet($aTrainingSet);
        // обучим модель
        $this->oModel->trainModel();
        // установим модель для анализа кач-ва
        $this->oQuality->setModel($this->oModel);
        // проведем анализ качества
        $this->oQuality->getQualityAnalysis();
        // обновим для сущности БД те показатели, которые могли измениться
        $oModel->coefficients = json_encode($this->oModel->getCoefficients());
        $oModel->threshold = $this->oQuality->getThreshold();
        $oModel->std_coeff = json_encode($this->oQuality->getStdCoeff());
        $oModel->elastic_coeff = json_encode($this->oQuality->getElasticCoeff());
        $oModel->curve_area = $this->oQuality->getCurveArea();
        $oModel->sill = $this->oQuality->getSill();
        // если порог стал меньше минимального значения, то отправим администратору письмо
        if($oModel->threshold < $oModel->min_threshold)
            Mail::send('emails.evaluations.model_broke', ['model_name' => $oModel->name, 'model_id' => $oModel->id], function($message)
            {
                $message->to(Config::get('app.admin_email'), 'Администратор')->subject('Порог отсечения модели стал ниже минимального значения');
            });
        // сохраним сущность в БД
        return $oModel->save();
    }

}