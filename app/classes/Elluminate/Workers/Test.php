<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 19.02.15
 * Time: 16:53
 */
namespace Elluminate\Workers;
class Test {
    public function fire($job, $data) {
        sleep(60*2);
        $duration = new \Duration();
        $duration->name = $data['name'];
        $duration->duration = $data['duration'];
        $duration->save();
        $job->delete();
    }
}