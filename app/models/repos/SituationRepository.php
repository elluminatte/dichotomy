<?php

/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 30.01.15
 * Time: 15:34
 */
class SituationRepository
{

    /** получает список проблемных ситуаций
     * @param $iParentSituationId - id ситуации, наслдеников которой надо получить
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSituationsList($iParentSituationId, $needModels = false, $needChildren = false)
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
        if($needModels)
            $oSituations->load('modelsId');
        if($needChildren)
            $oSituations->load('children');
        return $oSituations;
    }
}