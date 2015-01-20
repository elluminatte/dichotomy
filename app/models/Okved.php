<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 20.01.15
 * Time: 15:45
 */
class Okved extends Eloquent {
    protected $table = 'okved';
    public $timestamps = false;
    public function childs() {
        return $this->hasMany('Okved', 'parent_id');
    }

    public function parent() {
        return $this->belongsTo('Okved');
    }
}