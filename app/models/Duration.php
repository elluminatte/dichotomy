<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 30.01.15
 * Time: 12:51
 */
class Duration extends Eloquent {

    /**
     * @var bool - не надо использовать поля "создана в" и "изменено в", которые включены по умолчанию
     */
    public $timestamps = false;
}