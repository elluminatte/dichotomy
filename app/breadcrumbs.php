<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 23.01.15
 * Time: 13:08
 */
Breadcrumbs::register('okvedList', function($breadcrumbs, $sectionTree, $mode = 'list') {
    $breadcrumbs->push('Разделы ОКВЭД', route('okvedList'));
    if(is_array($sectionTree) && count($sectionTree))
        foreach($sectionTree as $section) {
            $breadcrumbs->push($section['name'], URL::route('okvedList', array('parentId' => $section['id'])));
        }
    switch($mode) {
        case 'list':
            break;
        case 'edit':
            $breadcrumbs->push('Редактирование раздела');
            break;
        case 'add':
            $breadcrumbs->push('Добавление раздела');
    }
});