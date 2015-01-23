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
        $breadcrumbs = $this->okved->makeBreadCrumbs($parentId);
        return View::make('admin.okved.addForm', array('parentId' => $parentId, 'breadcrumbs' => $breadcrumbs));
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
        $fields = ['name' => $name, 'okved_correspondence' => $okved_correspondence, 'parentId' => $parentId];
        $rules = ['name' => array('required', 'min:10', 'alpha_spaces'), 'parentId' => array('integer')];
        $fieldNames = ['name' => 'Название', 'okved_correspondence' => 'Соответствие ОКВЭД', 'parentId' => 'Номер родительского раздела'];
        $validation = \Validator::make($fields, $rules, array(), $fieldNames);
        if ($validation->fails())
        {
//            если валидация провалилась, возвращаемся на форму и показываем ошибки
            return Redirect::route('addOkvedForm', array('parentId' => $parentId))->withErrors($validation);
        }
//        добавляем раздел, формируем вид
        $result = $this->okved->addSection($name, $okved_correspondence, $parentId) ? array(1) : array();
        return View::make('admin.okved.addResult', array('result' => $result));
    }

    /** показывает форму редактирования раздела ОКВЭД
     * @param $sectionId - id раздела для редактирования
     * @return \Illuminate\View\View
     */
    public function showEditForm($sectionId) {
        $sectionId = (int)$sectionId;
        $breadcrumbs = $this->okved->makeBreadCrumbs($sectionId);
        $section = SimplifiedOkved::find($sectionId);
        return View::make('admin.okved.editForm', array('section' => $section, 'breadcrumbs' => $breadcrumbs));
    }

    /** изменяет данные раздела ОКВЭД
     * @return \Illuminate\View\View
     */
    public function editSection() {
        $sectionId = Input::get('section_id');
        $name = Input::get('name');
        $okved_correspondence = Input::get('okved_correspondence');
//        ToDo: валидация
        //        валидируем данные  по правилам
        $fields = ['name' => $name, 'okved_correspondence' => $okved_correspondence, 'sectionId' => $sectionId];
        $rules = ['name' => array('required', 'min:10', 'alpha_spaces'), 'sectionId' => array('integer')];
        $fieldNames = ['name' => 'Название', 'okved_correspondence' => 'Соответствие ОКВЭД', 'parentId' => 'Номер раздела'];
        $validation = \Validator::make($fields, $rules, array(), $fieldNames);
        if ($validation->fails())
        {
//            если валидация провалилась, возвращаемся на форму и показываем ошибки
            return Redirect::route('editOkvedForm', array('sectionId' => $sectionId))->withErrors($validation);
        }
        $result = $this->okved->updateSection($name, $okved_correspondence, $sectionId) ? array(1) : array();
        return View::make('admin.okved.editResult', array('result' => $result));
    }
}