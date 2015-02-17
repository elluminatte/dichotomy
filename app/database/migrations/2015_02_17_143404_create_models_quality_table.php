<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelsQualityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('models_quality', function($table) {
			$table->increments('id')->unsigned();
			$table->integer('model_id')->unsigned();
			$table->integer('threshold')->unsigned();
			$table->text('std_coeff');
			$table->text('elastic_coeff');
			$table->float('curve_area')->unsigned();
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
		//
		Schema::table('models_quality', function (Blueprint $table) {
			$table->dropForeign('models_quality_model_id_foreign');
		});
		Schema::drop('models_quality');

	}

}
