<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 31.01.15
 * Time: 14:38
 */
namespace Elluminate\Composers;

/** составитель шаблона для верхнего меню
 * Class NavbarComposer
 * @package Elluminate\Composers
 */
class NavbarComposer {

    /** функция будет вызываться каждый раз, когда будет отрисовываться нужный шаблон
     * регистрация составителя находится в app/composers.php
     * @param $view
     */
    public function compose($view) {
        if(true) {
            $view->with('test', [1]);
            return;
        }
        if(true) {
            $view->with('test', [5,6,7]);
            return;
        }
    }


}