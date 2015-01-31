<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 20.01.15
 * Time: 15:45
 */
class Situation extends Eloquent {

    /**
     * @var bool - не надо использовать поля "создана в" и "изменено в", которые включены по умолчанию
     */
    public $timestamps = false;

    protected static $aFieldNames = [
        'id' => 'Идентификатор',
        'name' => 'Название',
        'okved_correspondence' => 'Соответствие ОКВЭД',
        'parent_id' => 'Номер родительской ситуации'
    ];

    protected static $aValidRules = [
        'id' => 'Integer',
        'name' => 'Required|Min:5',
        'parent_id' => 'Integer'
    ];

    /** получает родителя записи (реализована рекурсивная связь)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        return $this->belongsTo('Situation', 'parent_id', 'id');
    }

    /** получает наследников записи (реализована рекурсивная связь)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {
        return $this->hasMany('Situation', 'parent_id', 'id');
    }

    public function models() {
        return $this->hasMany('Model');
    }

    public static function validate($input) {
        return \Validator::make($input, self::$aValidRules, array(), self::$aFieldNames);
    }
}