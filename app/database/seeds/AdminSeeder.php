<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 19.01.15
 * Time: 13:22
 */

class AdminSeeder extends Seeder {

    public function run()
    {
        $user = new User;
        $user->username = 'admin';
        $user->email = 'elluminatte@icloud.com';
        $user->password = 'admin';
        $user->password_confirmation = 'admin';
        $user->confirmation_code = md5(uniqid(mt_rand(), true));
        if(! $user->save()) {
            Log::info('Unable to create user '.$user->username, (array)$user->errors());
        } else {
            Log::info('Created user "'.$user->username.'" <'.$user->email.'>');
        }

        $admin = new Role;
        $admin->name = 'administrator';
        $admin->save();
        $userRole = Role::where('name', '=', 'user')->first();
        $user = User::where('username','=','admin')->first();
        $user->attachRole($admin);
        $user->attachRole($userRole);
    }

}