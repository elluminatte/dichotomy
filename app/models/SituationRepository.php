<?php

/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 30.01.15
 * Time: 15:34
 */
class SituationRepository
{

    // используем общий трейт
    use Elluminate\Repositories\HierarchicalRepository;

    /** получает список проблемных ситуаций
     * @param $iParentSituationId - id ситуации, наслдеников которой надо получить
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSituationsList($iParentSituationId)
    {
        $iParentSituationId = (int)$iParentSituationId;
        // если это не верхний уровень и нет такого раздела, значит нас хотят обмануть
        // или просто такой раздел не сущесвует, отдадим 404 ошибку
        if ($iParentSituationId !== 0 && !Situation::find($iParentSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // если верхний уровень, отдадим разделы, у которых родителя нет
        if (!$iParentSituationId)
            $oSituations = Situation::whereNull('parent_id')->get();
        // иначе найдем наследников
        else
            $oSituations = Situation::find($iParentSituationId)->children()->get();
        return $oSituations;
    }


    /** добавляет новую ситуацию
     * @param $sName - название
     * @param $sOkvedCorrespondence - соответствие ОКВЭД
     * @param $iParentId - id ситуации-родителя
     * @return bool
     */
    public function storeSituation($sName, $sOkvedCorrespondence, $iParentId)
    {
        // кое-как обработали данные, внутри PDO, с экранированием заморачиваться не надо
        $sName = (string)$sName;
        $sOkvedCorrespondence = (string)$sOkvedCorrespondence;
        $iParentId = (int)$iParentId;
        // проверяем не хотят ли нас обмануть - существует ли такой раздел
        if ($iParentId !== 0 && !Situation::find($iParentId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // создаем сущность
        $oSituation = new Situation();
        // задаем ей атрибуты
        $oSituation->name = $sName;
        $oSituation->okved_correspondence = $sOkvedCorrespondence;
        // если родителя нет, то null подставится в СУБД
        if ($iParentId !== 0)
            $oSituation->parent_id = $iParentId;
        // сохраним сущность
        return $oSituation->save();
    }

    /** обновляет данные о проблемной ситуации
     * @param $iSituationId - id ситуации, которую обновляем
     * @param $sName - название
     * @param $sOkvedCorrespondence - соответствие ОКВЭД
     * @return bool
     */
    public function updateSituation($iSituationId, $sName, $sOkvedCorrespondence)
    {
        $iSituationId = (int)$iSituationId;
        // проверяем не хотят ли нас обмануть - существует ли такой раздел
        if (!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // кое-как обработали данные, внутри PDO, с экранированием заморачиваться не надо
        $sName = (string)$sName;
        $sOkvedCorrespondence = (string)$sOkvedCorrespondence;
        // нашли сущность
        $oSituation = Situation::find($iSituationId);
        // записали атрибуты
        $oSituation->name = $sName;
        $oSituation->okved_correspondence = $sOkvedCorrespondence;
        // сохранили
        return $oSituation->save();
    }

    /** удаляет проблемную ситуацию
     * @param $iSituationId - id ситуации, которую будем удалять
     * @return bool|null
     * @throws Exception
     */
    public function destroySection($iSituationId)
    {
        $iSituationId = (int)$iSituationId;
        // проверяем не хотят ли нас обмануть - существует ли такой раздел
        if (!$iSituationId || !Situation::find($iSituationId, ['id'])) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        // удаляем сущность, о вложенных позаботиться СУБД с помощью внешних ключей
        return Situation::find($iSituationId)->delete();
    }


}