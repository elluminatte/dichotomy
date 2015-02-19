<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('evaluations', function($table) {
			$table->increments('id')->unsigned();
			$table->smallInteger('estimated_result')->unsigned();
			$table->smallInteger('real_result')->unsigned();
			$table->text('covariates');
			$table->dateTime('expired_moment');
			$table->integer('user_id')->unsigned();
			$table->integer('model_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')
				->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('model_id')->references('id')->on('models')
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
		Schema::drop('evaluations');
	}

}
