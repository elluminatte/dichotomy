<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 27.01.15
 * Time: 15:11
 */
class SimplifiedOkved extends Eloquent {

    /**
     * @var bool - не надо использовать поля "создана в" и "изменено в", которые включены по умолчанию
     */
    public $timestamps = false;
}