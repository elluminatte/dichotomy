<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 19.01.15
 * Time: 17:13
 */
class UserRoleSeeder extends Seeder {

    public function run()
    {
        $user = new Role;
        $user->name = 'user';
        $user->save();
    }
}