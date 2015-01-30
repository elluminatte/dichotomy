<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('models', function($table) {
			$table->increments('id')->unsigned();
			$table->integer('situation_id')->unsigned();
			$table->string('name');
			$table->text('comment')->nullable();
			$table->text('titles');
			$table->text('coefficients');
			$table->smallInteger('durations_id')->unsigned();
			$table->smallInteger('threshold')->unsigned();
			$table->smallInteger('min_threshold')->unsigned();
			$table->text('core_selection');
			$table->text('oversampling')->nullable();
			$table->foreign('situation_id')->references('id')->on('situations')
				->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('durations_id')->references('id')->on('durations')
				->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('models');
		Schema::table('models', function (Blueprint $table) {
//			$table->dropForeign('models_durations_id_foreign');
//			$table->dropForeign('models_simplified_okved_id_foreign');
		});
	}

}
