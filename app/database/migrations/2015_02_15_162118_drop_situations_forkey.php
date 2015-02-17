<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSituationsForkey extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// нужно убрать внешний ключ, чтобы перенсти данные с хостинга
//		Schema::table('situations', function (Blueprint $table) {
//			$table->dropForeign('situations_parent_id_foreign');
//		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// возвращаем ограничение
//		Schema::table('situations', function (Blueprint $table) {
//			$table->foreign('parent_id')->references('id')->on('situations')->onDelete('cascade')->onUpdate('cascade');
//		});
	}

}
