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
    public function getSections($sectionId) {
        $sectionId = (int)$sectionId;
        // если есть родитель - получаем его наследников
        if($sectionId)
            $sections = SimplifiedOkved::where('parent_id', '=', $sectionId)->get();
        // иначе - получаем разделы верхнего уровня, у которых родителей нет, они null
        else
            $sections = SimplifiedOkved::whereNull('parent_id')->get();
        return $sections;
    }

    /** удаляет раздел и его наследников по id
     * @param $sectionId - id раздела
     * @return bool - истина, если удалилось, иначе ложь
     * @throws Exception
     */
    public function delSection($sectionId) {
        $sectionId = (int)$sectionId;
        $section = SimplifiedOkved::find($sectionId);
        if($section && $section instanceof SimplifiedOkved) {
            $section->delete();
            return true;
        }
        return false;
    }

    /** добавляет раздел ОКВЭД
     * @param $name - название раздела
     * @param $okved_correspondence - соответствие ОКВЭД
     * @param $parentId - id раздела-родителя
     * @return bool - истина, если добавилось, иначе - ложь
     */
    public function addSection($name, $okved_correspondence, $parentId) {
        $parentId = (int)$parentId;
        $okved = new SimplifiedOkved();
        $okved->name = $name;
        $okved->okved_correspondence = $okved_correspondence;
        if($parentId !== 0)
            $okved->parent_id = $parentId;
        return $okved->save();
    }
}