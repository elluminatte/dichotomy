<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// создаем таблицу с возможными вариантами корректности решения
		Schema::create('durations', function( $table ) {
			$table->increments('id')->unsigned();
			$table->string('name', 50);
			//будем хранить в часах, а потом добавлять с помощью Carbon
			$table->smallInteger('duration')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//удаляем таблицу при откате миграции
		Schema::drop('durations');
		//
	}

}
