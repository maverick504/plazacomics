<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSeriesAuthorsTable extends Migration {

	public function up()
	{
		Schema::create('series_authors', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('serie_id')->unsigned();
			$table->integer('user_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('series_authors');
	}
}
