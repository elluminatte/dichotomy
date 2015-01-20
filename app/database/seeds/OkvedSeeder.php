<?php
/**
 * Created by PhpStorm.
 * User: elluminate
 * Date: 20.01.15
 * Time: 15:42
 */
class OkvedSeeder extends Seeder {
    public function run() {

        DB::table('okved')->delete();

        $okRecord = new Okved;
        $okRecord->code = 'РАЗДЕЛ A';
        $okRecord->name = 'СЕЛЬСКОЕ ХОЗЯЙСТВО, ОХОТА И ЛЕСНОЕ ХОЗЯЙСТВО';
        $okRecord->additional_info = 'Раздел  A  охватывает  использование  природных ресурсов растительного и животного происхождения. Раздел включает деятельность    по    выращиванию   сельскохозяйственных культур,  разведению животных (как сельскохозяйственных, так  и  диких),  заготовке  леса  и  сбору  дикорастущих лесопродуктов,   а   также   деятельность,  связанную  с воспроизводством и защитой лесов';
        $okRecord->save();

        $okRecord = new Okved;
        $okRecord->code = 'Подраздел AA';
        $okRecord->name = 'СЕЛЬСКОЕ ХОЗЯЙСТВО, ОХОТА И ЛЕСНОЕ ХОЗЯЙСТВО';
        $okRecord->parent_id = 1;
        $okRecord->save();
    }
}