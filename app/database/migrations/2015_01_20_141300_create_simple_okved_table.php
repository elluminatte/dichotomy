<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimpleOkvedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// создаем таблицу упрощенный ОКВЭД
		Schema::create('simplified_okved', function( $table ) {
			$table->increments('id')->unsigned();
			$table->string('name', 512);
			$table->string('okved_correspondence', 16)->default('');
			$table->integer('parent_id')->unsigned()->nullable();
			$table->foreign('parent_id')->references('id')->on('simplified_okved')->onDelete('cascade')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// удаляем таблицу ОКВЭД при откате миграции
		Schema::drop('simplified_okved');
		Schema::table('simplified_okved', function (Blueprint $table) {
			$table->dropForeign('simplified_okved_parent_id_foreign');
		});
	}

}
