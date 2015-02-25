<?php

/** контроллер проблемных ситуаций для зарегистрированного пользователя
 * Class ClientSituationController
 */
class ClientSituationController extends \BaseController
{


    /**
     * @var ClientSituationRepository - репозиторий
     */
    protected $oRepo;

    public function __construct(ClientSituationRepository $oRepo)
    {
        $this->oRepo = $oRepo;
    }

    /** показывает список проблемных ситуаций
     * @param int $iParentSituationId - id родительской проблемной ситуации
     * @return \Illuminate\View\View
     */
    public function index($iParentSituationId = 0)
    {
        // собираем список разделов с моделями
        $oSituations = $this->oRepo->getSituationsList($iParentSituationId, true, true);
        // собираем дерево родителей для хлебных крошек
        $aHierarchy = \Elluminate\Engine\E::buildHierarchy($iParentSituationId);
        // отдаем данные в вид и рисуем его
        return View::make('client.situations.index', [
            'situations' => $oSituations,
            'hierarchy' => $aHierarchy
        ]);
    }

}
