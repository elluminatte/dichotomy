<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 21.01.15
 * Time: 17:41
 */

/**
 * Class SimplifiedOkvedRepository
 * Абстракция для работы с сущностью упрощенного ОКВЭД
 */

class SimplifiedOkvedRepository {
    /** получает дерево разделов ОКВЭД по родителю
     * @param $sectionId - id раздела-родителя
     * @return \Illuminate\Database\Eloquent\Collection|static[] - коллекция разделов
     */
    public function getOkvedSections($sectionId) {
        $sectionId = (int)$sectionId;
        // если есть родитель - получаем его наследников
        if($sectionId)
            $sections = SimplifiedOkved::where('parent_id', '=', $sectionId)->get();
        // иначе - получаем разделы верхнего уровня, у которых родителей нет, они null
        else
            $sections = SimplifiedOkved::whereNull('parent_id')->get();
        return $sections;
    }
}