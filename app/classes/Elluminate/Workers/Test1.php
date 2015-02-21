<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 19.02.15
 * Time: 16:53
 */
namespace Elluminate\Workers;
class Test1 {
    public function fire($job, $data) {
        $duration = new \Duration();
        $duration->name = $data['name'];
        $duration->duration = $data['duration'];
        $duration->save();
        $job->delete();
    }
}