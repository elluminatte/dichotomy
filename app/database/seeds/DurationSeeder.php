<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 30.01.15
 * Time: 12:50
 */
class DurationSeeder extends Seeder
{

    public function run()
    {

        DB::table('durations')->delete();

        $duration = new Duration();
        $duration->name = 'час';
        $duration->duration = 1;
        $duration->save();

        $duration = new Duration();
        $duration->name = 'день';
        $duration->duration = 24;
        $duration->save();

        $duration = new Duration();
        $duration->name = 'неделя';
        $duration->duration = 24*7;
        $duration->save();

        $duration = new Duration();
        $duration->name = 'месяц';
        $duration->duration = 24*30;
        $duration->save();

        $duration = new Duration();
        $duration->name = 'квартал';
        $duration->duration = 24*30*4;
        $duration->save();

        $duration = new Duration();
        $duration->name = 'год';
        $duration->duration = 365*24;
        $duration->save();
    }
}