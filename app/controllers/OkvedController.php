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
        //получим родителя до того, как удалили, чтобы потом можно было вернуться к разделу
        $parentId = $this->okved->getSectionParentId($sectionId);
        $result = $this->okved->delSection($sectionId);
        // магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата удаления
        if($result)
            return Redirect::route('okvedList', array('sectionId' => $parentId))->withForm_result('delDone');
        else
            return Redirect::route('okvedList', array('sectionId' => $parentId))->withForm_result('delFailed');
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
//            если валидация провалилась, возвращаемся на форму и показываем ошибки, заполняем поля, чтоб пользователю не писать заново
            return Redirect::route('addOkvedForm', array('parentId' => $parentId))->withErrors($validation)->withInput();
//        добавляем раздел, формируем вид
        $result = $this->okved->addSection($name, $okved_correspondence, $parentId);
        // магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата добавления
        if($result)
            return Redirect::route('okvedList', array('sectionId' => $parentId))->withForm_result('addDone');
        else
            return Redirect::route('okvedList', array('sectionId' => $parentId))->withForm_result('addFailed');
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
        $parentId = $this->okved->getSectionParentId($sectionId);
        //        валидируем данные  по правилам
        $fields = ['name' => $name, 'okved_correspondence' => $okved_correspondence, 'sectionId' => $sectionId];
        $rules = ['name' => array('required', 'min:10', 'alpha_spaces'), 'sectionId' => array('integer')];
        $fieldNames = ['name' => 'Название', 'okved_correspondence' => 'Соответствие ОКВЭД', 'parentId' => 'Номер раздела'];
        $validation = \Validator::make($fields, $rules, array(), $fieldNames);
        if ($validation->fails())
//            если валидация провалилась, возвращаемся на форму и показываем ошибки, заполняем поля, чтоб пользователю не писать заново
            return Redirect::route('editOkvedForm', array('sectionId' => $sectionId))->withErrors($validation)->withInput();
        $result = $this->okved->updateSection($name, $okved_correspondence, $sectionId);
        // магия - передает в вид переменную form_result - какой шаблон отрисовать в качестве результата изменения
        if($result)
            return Redirect::route('okvedList', array('sectionId' => $parentId))->withForm_result('editDone');
        else
            return Redirect::route('okvedList', array('sectionId' => $parentId))->withForm_result('editFailed');
    }
}