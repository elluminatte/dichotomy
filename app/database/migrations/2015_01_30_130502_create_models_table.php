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
			$table->text('cov_names');
			$table->text('cov_comments');
			$table->text('coefficients');
			$table->smallInteger('durations_id')->unsigned();
			$table->smallInteger('min_threshold')->unsigned();
			$table->text('core_selection');
			$table->text('oversampling')->nullable();
			$table->integer('threshold')->unsigned();
			$table->text('std_coeff');
			$table->text('elastic_coeff');
			$table->float('curve_area')->unsigned();
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
		Schema::table('models', function (Blueprint $table) {
			$table->dropForeign('models_durations_id_foreign');
			$table->dropForeign('models_situation_id_foreign');
		});
		Schema::drop('models');

	}

}
