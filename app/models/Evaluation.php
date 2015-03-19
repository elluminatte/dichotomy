<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 30.01.15
 * Time: 12:51
 */
class Evaluation extends Eloquent {

    /**
     * @var bool - не надо использовать поля "создана в" и "изменено в", которые включены по умолчанию
     */
    public $timestamps = false;

    protected static $aFieldNames = [
        'evaluation_id' => 'Идентификатор вычисления',
        'real_result' => 'Реальный результат'
    ];

    protected static $aValidRules = [
        'evaluation_id' => 'Integer',
        'real_result' => 'in:-1,0,1'

    ];


    public function model() {
        return $this->belongsTo('Model')->select('id', 'name');
    }

    public static function validate($input) {
        return \Validator::make($input, self::$aValidRules, array(), self::$aFieldNames);
    }
}