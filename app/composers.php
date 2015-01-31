<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 14:45
 */
// регистрация составителей шаблонов
View::composer('partials.navbar', '\Elluminate\Composers\NavbarComposer');