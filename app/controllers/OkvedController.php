<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 21.01.15
 * Time: 17:14
 */
class OkvedController extends BaseController {

    /**
     * @var SimplifiedOkvedRepository - репозиторий для хранения логики работы с сущностью ОКВЭД
     */
    private $okved;

    /**
     * @param SimplifiedOkvedRepository $okved - внедряем зависимость - репозиторий упрощенного ОКВЭД
     */
    public function __construct(SimplifiedOkvedRepository $okved) {
        $this->okved = $okved;
    }

    public function showOkvedTree($sectionId = 0) {
        $okvedSections = $this->okved->getOkvedSections($sectionId);
        return View::make('okved', array('okveds' => $okvedSections));
    }
}