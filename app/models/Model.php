<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 27.01.15
 * Time: 15:11
 */
class Model extends Eloquent {

    /**
     * @var bool - не надо использовать поля "создана в" и "изменено в", которые включены по умолчанию
     */
    public $timestamps = false;

    protected static $aFieldNames = [
        'id' => 'Идентификатор',
        'name' => 'Название',
        'duration' => 'Время корректности решения',
        'min_threshold' => 'Минимальный порог отсечения',
        'comment' => 'Информация о задаче',
        'train_file' => 'Файл обучающей выборки',
        'situation_id' => 'Идентификатор проблемной ситуации'
    ];

    protected static $aValidRules = [
        'id' => 'Integer',
        'name' => 'Required|Min:5',
        'duration' => 'Required|Integer',
        'min_threshold' => 'Required|Between:0,100',
        'situation_id' => 'Required|Integer',
        'train_file' => 'Required|mimes:xls,xlsx'

    ];

    public function duration() {
        return $this->belongsTo('Duration', 'durations_id');
    }

    public function situation() {
        return $this->belongsTo('Situation');
    }

    public static function validate($input) {
        return \Validator::make($input, self::$aValidRules, array(), self::$aFieldNames);
    }
}