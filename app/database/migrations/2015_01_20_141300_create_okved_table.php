<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOkvedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// создаем таблицу ОКВЭД
		Schema::create('okved', function( $table ) {
			$table->increments('id');
			$table->string('code', 16)->unique();
			$table->string('name', 512)->unique();
			$table->integer('parent_id')->default(0);
			$table->text('additional_info')->nullable();
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
		Schema::drop('okved');
	}

}
