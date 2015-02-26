<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 23.01.15
 * Time: 13:08
 */
// тут будем хранить механизмы генерации хлебных крошек для разных модулей приложения

// крошки для проблемных ситуаций админу
Breadcrumbs::register('admin.situations', function($oBreadcrumbs, $aHierarchy, $sMode) {
    $oBreadcrumbs->push('Главная', url('/'));
    // первый уровень-заглушка
    $oBreadcrumbs->push('Каталог проблемных ситуаций', route('situations.list'));
    if(is_array($aHierarchy) && count($aHierarchy))
        foreach($aHierarchy as $section) {
            $oBreadcrumbs->push($section['name'], URL::route('situations.list', array('iParentSituationId' => $section['id'])));
        }
    switch($sMode) {
        case 'list':
            break;
        // если показали форму редактирования, то надо добавить заглушку
        case 'edit':
            $oBreadcrumbs->push('Редактирование реквизитов');
            break;
        // аналогично редактированию
        case 'create':
            $oBreadcrumbs->push('Добавление ситуации');
    }
});
// крошки для задач классификации админу
Breadcrumbs::register('admin.models', function($oBreadcrumbs, $aHierarchy, $sMode, $iModelId = null) {
    $oBreadcrumbs->push('Главная', url('/'));
    // первый уровень-заглушка
    $oBreadcrumbs->push('Каталог проблемных ситуаций', route('situations.list'));
    if(is_array($aHierarchy) && count($aHierarchy))
        foreach($aHierarchy as $section) {
            $oBreadcrumbs->push($section['name'], URL::route('situations.list', array('iParentSituationId' => $section['id'])));
        }
    switch($sMode) {
        case 'list':
            break;
        case 'detail':
            if(!is_null($iModelId) && \Model::find($iModelId, ['id']))
                $oBreadcrumbs->push(\Model::find($iModelId, ['name'])->name);
            break;
        case 'create':
            $oBreadcrumbs->push('Добавление задачи');
            break;
        case 'edit':
            if(!is_null($iModelId) && \Model::find($iModelId, ['id']))
                $oBreadcrumbs->push(\Model::find($iModelId, ['name'])->name, URL::route('models.detail', ['iModelId' => $iModelId]));
            $oBreadcrumbs->push('Редактирование параметров');
            break;
    }
});

// крошки для проблемных ситуаций пользователю
Breadcrumbs::register('client.situations', function($oBreadcrumbs, $aHierarchy) {
    $oBreadcrumbs->push('Главная', url('/'));
    // первый уровень-заглушка
    $oBreadcrumbs->push('Каталог проблемных ситуаций', route('problems.list'));
    if(is_array($aHierarchy) && count($aHierarchy))
        foreach($aHierarchy as $section) {
            $oBreadcrumbs->push($section['name'], URL::route('problems.list', array('iParentSituationId' => $section['id'])));
        }
});

// крошки для задач классификации пользователю
Breadcrumbs::register('client.models', function($oBreadcrumbs, $aHierarchy, $sMode, $iModelId = null) {
    $oBreadcrumbs->push('Главная', url('/'));
    // первый уровень-заглушка
    $oBreadcrumbs->push('Каталог проблемных ситуаций', route('problems.list'));
    if(is_array($aHierarchy) && count($aHierarchy))
        foreach($aHierarchy as $section) {
            $oBreadcrumbs->push($section['name'], URL::route('problems.list', array('iParentSituationId' => $section['id'])));
        }
    switch($sMode) {
        case 'list':
            break;
        case 'detail':
            if(!is_null($iModelId) && \Model::find($iModelId, ['id']))
                $oBreadcrumbs->push(\Model::find($iModelId, ['name'])->name);
            break;
    }
});

Breadcrumbs::register('evaluations', function($oBreadcrumbs, $sMode, $iModelId = null) {
    $oBreadcrumbs->push('Главная', url('/'));
    $oBreadcrumbs->push('Обратная связь', route('evaluations.list'));
    switch($sMode) {
        case 'list':
            break;
        case 'detail':
            if(!is_null($iModelId) && \Model::find($iModelId, ['id']))
                $oBreadcrumbs->push(\Model::find($iModelId, ['name'])->name);
            break;
    }
});

Breadcrumbs::register('models.inactive', function($oBreadcrumbs) {
    $oBreadcrumbs->push('Главная', url('/'));
    $oBreadcrumbs->push('Неактивные задачи', route('models.inactive'));
});

