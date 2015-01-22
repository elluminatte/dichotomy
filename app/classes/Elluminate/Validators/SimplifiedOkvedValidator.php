<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 22.01.15
 * Time: 15:45
 */
namespace Elluminate\Validators;

/** Валидатор для форм ОКВЭД
 * Class SimplifiedOkvedValidator
 * @package Elluminate\Validators
 */
class SimplifiedOkvedValidator {

    /**
     * @var array - правила валидации
     */
    private $rules = array(
        'name' => array('required', 'min:10', 'alpha_spaces'),
        'parentId' => array('integer')
    );

    /**
     * @var array - человекопонятные имена полей формы
     */
    private $attributeNames = array(
        'name' => 'Название',
        'okved_correspondence' => 'Соответствие ОКВЭД',
        'parentId' => 'Номер родительского раздела',
    );

    /**
     * @var array - поля, которые нужно провалидировать
     */
    private $fields = array();

    public function __construct($name, $okved_correspondence, $parentId) {
        $this->fields = array(
            'name' => $name,
            'okved_correspondence' => $okved_correspondence,
            'parentId' => $parentId
        );
    }

    /** валидирует форму
     * @return \Illuminate\Validation\Validator
     */
    public function validate() {
        return $validation = \Validator::make($this->fields, $this->rules, array(), $this->attributeNames);
    }
}