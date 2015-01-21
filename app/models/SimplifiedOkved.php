<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 20.01.15
 * Time: 15:45
 */
class SimplifiedOkved extends Eloquent {


    /**
     * @var string - имя таблицы, так как оно не совпадает с множественным числом имени класса
     */
    protected $table = 'simplified_okved';

    /**
     * @var bool - не надо использовать поля "создана в" и "изменено в", которые включены по умолчанию
     */
    public $timestamps = false;

    /** получает родителя записи (реализована рекурсивная связь)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        return $this->belongsTo('SimplifiedOkved', 'parent_id');
    }

    /** получает наследников записи (реализована рекурсивная связь)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {
        return $this->hasMany('SimplifiedOkved', 'parent_id');
    }
}