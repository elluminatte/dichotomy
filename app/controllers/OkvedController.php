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

    /** показывает дерево разделов ОКВЭД
     * @param int $sectionId - id раздела, наследников которого нужно показать
     * @return \Illuminate\View\View
     */
    public function showTree($sectionId = 0) {
        $sections = $this->okved->getSections($sectionId);
        $breadcrumbs = $this->okved->makeBreadCrumbs($sectionId);
        return View::make('admin.okved.tree', array('sections' => $sections, 'parentId' => $sectionId, 'breadcrumbs' => $breadcrumbs));
    }

    /** удаляет раздел по id
     * @param int $sectionId - id раздела, который надо удалить
     * @return \Illuminate\View\View
     */
    public function deleteSection($sectionId = 0) {
        $result = $this->okved->delSection($sectionId) ? array(1) : array();
        return View::make('admin.okved.delResult', array('result' => $result));
    }

    /** показывает форму для добавления раздела
     * @param int $sectionId - id раздела, который станет родителем для добавленного
     * @return \Illuminate\View\View
     */
    public function showAddForm($parentId = 0) {
        $parentId = (int)$parentId;
        return View::make('admin.okved.addForm', array('parentId' => $parentId));
    }

    /** добавляет раздел ОКВЭД
     * @return $this|\Illuminate\View\View
     */
    public function addSection() {
//        получили данные из формы
        $name = Input::get('name');
        $okved_correspondence = Input::get('okved_correspondence');
        $parentId = Input::get('parent_id', 0);
//        валидируем данные  по правилам
        $validator = new Elluminate\Validators\SimplifiedOkvedValidator($name, $okved_correspondence, $parentId);
        $validation = $validator->validate();
        if ($validation->fails())
        {
//            если валидация провалилась, возвращаемся на форму и показываем ошибки
            return Redirect::route('addOkvedForm', array('parentId' => $parentId))->withErrors($validation);
        }
//        добавляем раздел, формируем вид
        $result = $this->okved->addSection($name, $okved_correspondence, $parentId) ? array(1) : array();
        return View::make('admin.okved.addResult', array('result' => $result));
    }

    public function showEditForm($sectionId) {
        $sectionId = (int)$sectionId;
        $section = SimplifiedOkved::find($sectionId);
        return View::make('admin.okved.editForm', array('section' => $section));
    }
    public function editSection() {
        $sectionId = Input::get('section_id');
        $name = Input::get('name');
        $okved_correspondence = Input::get('okved_correspondence');
//        ToDo: валидация
        $result = $this->okved->updateSection($name, $okved_correspondence, $sectionId) ? array(1) : array();
        return View::make('admin.okved.editResult', array('result' => $result));
    }
}