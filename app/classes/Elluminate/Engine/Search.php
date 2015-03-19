<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 18.02.15
 * Time: 14:32
 */
namespace Elluminate\Engine;

/**
 * Class Search - реализует механизм поиска по приложению
 * @package Elluminate\Engine
 */
class Search
{

    /**
     * @var - текст, введенный пользователем для поиска
     */
    private $sSearchText;

    /**
     * - максимальное кол-во резултатов по каждой группе
     */
    const GROUP_LIMIT = 10;

    /**
     * @var array - результаты поиска по коду ОКВЭД
     */
    private $aOkvedCodeResult;

    /**
     * @var - результаты поиска по названию проблемной ситуации
     */
    private $aSituationNameResult;

    /**
     * @var - результаты поиска по названию решаемой задачи
     */
    private $aModelNameResult;

    /**
     * @var - флаг переполнения по коду ОКВЭД
     */
    private $bOkvedCodeOverlimit;

    /**
     * @var - флаг переполнения по названию проблемной ситуации
     */
    private $bSituationNameOverlimit;

    /**
     * @var bool - флаг переполнения по названию решаемой задачи
     */
    private $bModelNameOverLimit;

    /** геттер результатов поиска по коду ОКВЭД
     * @return mixed
     */
    public function getOkvedCodeResult()
    {
        return $this->aOkvedCodeResult;
    }

    /** геттер результатов по названию проблемной ситуации
     * @return mixed
     */
    public function getSituationNameResult()
    {
        return $this->aSituationNameResult;
    }

    /** геттер результатов по названию решаемой задачи
     * @return mixed
     */
    public function getModelNameResult()
    {
        return $this->aModelNameResult;
    }

    /** сеттер поисковой фразы
     * @param mixed $sSearchText
     */
    public function setSearchText($sSearchText)
    {
        $this->sSearchText = $sSearchText;
    }

    /** геттер флага переполнения по коду ОКВЭД
     * @return mixed
     */
    public function getOkvedCodeOverlimit()
    {
        return $this->bOkvedCodeOverlimit;
    }

    /** геттер флага переполнения по названию проблемной ситуации
     * @return mixed
     */
    public function getSituationNameOverlimit()
    {
        return $this->bSituationNameOverlimit;
    }

    /** геттер флага переполнения по названию решаемой проблемы
     * @return mixed
     */
    public function getModelNameOverLimit()
    {
        return $this->bModelNameOverLimit;
    }

    public function __construct()
    {
        // инициализируем и обнулим результаты поиска
        $this->aOkvedCodeResult = $this->aSituationNameResult = $this->aModelNameResult = [];
        $this->bModelNameOverLimit = $this->bOkvedCodeOverlimit = $this->bSituationNameOverlimit = false;
    }

    /**
     * реализует поиск по приложению
     */
    public function startSearch()
    {
        // ищем по названию решаемой задачи
        $this->aModelNameResult = $this->findByModelName();
        // по названию проблемной ситуации
        $this->aSituationNameResult = $this->findBySituationName();
        // по коду ОКВЭД
        $this->aOkvedCodeResult = $this->findByOkvedCode();
    }

    /** ищет по названию решаемой задачи
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    private function findByModelName()
    {
        // посчитаем сколько всего записей подошло под условия
        $iCount = \Model::where('name', 'LIKE', "%$this->sSearchText%")->count('id');
        // если больше, чем надо для вывода, установим флаг
        if ($iCount > self::GROUP_LIMIT)
            $this->bModelNameOverLimit = true;
        return \Model::where('name', 'LIKE', "%$this->sSearchText%")->where('threshold', '>=', \DB::raw('min_threshold'))->take(self::GROUP_LIMIT)->get(['id', 'name']);
    }

    /** ищет по названию проблемной ситуации
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    private function findBySituationName()
    {
        // посчитаем сколько всего записей подошло под условия
        $iCount = \Situation::where('name', 'LIKE', "%$this->sSearchText%")->count('id');
        // если больше, чем надо для вывода, установим флаг
        if ($iCount > self::GROUP_LIMIT)
            $this->bSituationNameOverlimit = true;
        return \Situation::where('name', 'LIKE', "%$this->sSearchText%")->take(self::GROUP_LIMIT)->get();
    }

    /** ищет по коду ОКВЭД
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    private function findByOkvedCode()
    {
        // посчитаем сколько всего записей подошло под условия
        $iCount = \Situation::where('okved_correspondence', 'LIKE', "%$this->sSearchText%")->count('id');
        // если больше, чем надо для вывода, установим флаг
        if ($iCount > self::GROUP_LIMIT)
            $this->bOkvedCodeOverlimit = true;
        return \Situation::where('okved_correspondence', 'LIKE', "%$this->sSearchText%")->take(self::GROUP_LIMIT)->get();
    }
}