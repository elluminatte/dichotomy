<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 25.02.15
 * Time: 12:23
 */
class AdminSituationRepository extends SituationRepository {
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




}