<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 23.01.15
 * Time: 13:08
 */
Breadcrumbs::register('okvedList', function($breadcrumbs, $sectionTree) {
    $breadcrumbs->push('Разделы ОКВЭД', route('okvedList'));
    if(is_array($sectionTree) && count($sectionTree))
        foreach($sectionTree as $section) {
            $breadcrumbs->push($section['name'], URL::route('okvedList', array('parentId' => $section['id'])));
        }
});