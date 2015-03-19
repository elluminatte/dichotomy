<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 22.01.15
 * Time: 17:12
 */

//расширение правил вадидации

// пробелы и буквы
Validator::extend('alpha_spaces', function($attribute, $value)
{
    return preg_match('/^[\pL\s]+$/u', $value);
});