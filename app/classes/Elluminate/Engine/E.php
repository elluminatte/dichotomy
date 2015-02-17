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
        if (is_array($content)) {
            echo "<XMP>";
            print_r($content);
            echo "</XMP>";
        } else
            var_dump($content);
        if($die) exit;
    }

    public static function transliterate($string) {
        $string = (string)$string;
        //массив альтернатив
        $rus = array(' ', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ', 'э', 'ю', 'я', ' ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь', 'Ы', 'Ъ', 'Э', 'Ю', 'Я');
        $eng = array('_', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', "'", 'i', "'", 'e', 'yu', 'ya', '_', 'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'J', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', "'", 'I', "'", 'E', 'Yu', 'Ya');
        //заменяем русские английскими
        $string = str_replace($rus, $eng, $string);
        return $string;
    }





}