<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 06.01.15
 * Time: 15:19
 */

namespace Elluminate\Engine;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class E
 * @package Elluminate\Engine
 */
class E {

    /** построчно собирает данные из файла xls или xlsx
     * @param $fileName - имя файла
     * @param int $offset - с какой строки надо читать
     * @param int $rowLimit - сколько строк нужно прочитать
     * @param int $colLimit - скольок столбцов нужно прочитать
     * @param int $sheetNumber - номер листа книги файла
     * @return array - массив данных, содержащихся в файле
     */
    public static function getDataFromExcel($fileName, $offset = 0, $rowLimit = 0, $colLimit = 0, $sheetNumber = 0) {
        $data = array();
        try {
            $inputFileType = PHPExcel_IOFactory::identify($fileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
//            видит непустую ячейку только там, где есть реальное значение, а не стиль и т.д.
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($fileName);}
        catch (\Exception $e) {
            throw new FileException;
        }
        $sheet = $objPHPExcel->getSheet($sheetNumber);
//        чтобы не считать ячейки, где есть, например, стиль. должно помочь вместе с setReadDataOnly
//        если заданы ограничения, то читаем до них, иначе - читаем всё, что есть
        $highestRow = $rowLimit ? $rowLimit : $sheet->getHighestDataRow();
        $highestColumn = $colLimit ? $colLimit : $sheet->getHighestDataColumn();

        for ($row = $offset; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row);
            $data[] = $rowData;
        }
        return $data;
    }

    /** функция для отладки, выводит на экран содержимое переменной
     * @param $content - переменная, которую нужно вывести
     * @param bool $die - закончить ли выполнение программы после вывода значения
     */
    public static function _dumpContent($content, $die = true) {
        if (is_array($content) || is_object($content)) {
            echo "<XMP>";
            print_r($content);
            echo "</XMP>";
        } else
            var_dump($content);
        if($die) exit;
    }

    /** транслитирирует строку
     * @param $string - входная строка
     * @return mixed|string - результат транслитерации, кирилл. символы => латинские, пробелы => _
     */
    public static function transliterate($string) {
        $string = (string)$string;
        //массив альтернатив
        $rus = array(' ', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ', 'э', 'ю', 'я', ' ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь', 'Ы', 'Ъ', 'Э', 'Ю', 'Я');
        $eng = array('_', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', "'", 'i', "'", 'e', 'yu', 'ya', '_', 'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'J', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', "'", 'I', "'", 'E', 'Yu', 'Ya');
        //заменяем русские английскими
        $string = str_replace($rus, $eng, $string);
        return $string;
    }

    /** возвращает букву по ее позиции в алфавите
     * сейчас работает только для англ. алфавита и с позицией не больше, чем кол-во символов алфавита
     * мне больше и не нужно
     * @param $iPos - позиция буквы
     * @return string - буква алфавита
     */
    public static function findLetterByPos($iPos) {
        $iPos = (int)$iPos;
        // начальная позиция - начало алвафита
        if(!$iPos) return 'A';
        $sLetter = 'A';
        // сдвинем ее на позицию нужной буквы
        for($i=1; $i <= $iPos; $i++)
            $sLetter++;
        return $sLetter;
    }

    /** собирает иерархическое дерево проблемных ситуаций в цепочку
     * @param $iSituationId - id нижней ситуации
     * @return array - иерархия
     */
    public static function buildHierarchy($iSituationId) {
        $iSituationId = (int)$iSituationId;
        if(!$iSituationId) return [];
        $aHierarchy = [];
        $oSituation = \Situation::find($iSituationId);
        if(!$oSituation) return [];
        // сначала запихнем сам этот раздел
        array_push($aHierarchy, ['id' => $oSituation->id, 'name' => $oSituation->name]);
        // пока есть предки будем собирать их в цепочку
        while($oSituation->parent()->get()->first()) {
            $oParent = $oSituation->parent()->get()->first();
            array_unshift($aHierarchy, ['id' => $oParent->id, 'name' => $oParent->name]);
            $oSituation = $oParent;
        }
        return $aHierarchy;
    }





}