<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 23.01.15
 * Time: 13:08
 */
// тут будем хранить механизмы генерации хлебных крошек для разных модулей приложения

// крошки для проблемных ситуаций
Breadcrumbs::register('situations', function($breadcrumbs, $sectionTree, $mode = 'list') {
    // первый уровень-заглушка
    $breadcrumbs->push('Каталог проблемных ситуаций', route('situations.list'));
    if(is_array($sectionTree) && count($sectionTree))
        foreach($sectionTree as $section) {
            $breadcrumbs->push($section['name'], URL::route('situations.list', array('parentId' => $section['id'])));
        }
    switch($mode) {
        case 'list':
            break;
        // если показали форму редактирования, то надо добавить заглушку
        case 'edit':
            $breadcrumbs->push('Редактирование реквизитов');
            break;
        // аналогично редактированию
        case 'add':
            $breadcrumbs->push('Добавление ситуации');
    }
});