<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSeriesTable extends Migration {

	public function up()
	{
		Schema::create('series', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name')->unique();
			$table->string('slug')->unique();
			$table->char('state', 10);
			$table->smallInteger('genre1')->unsigned();
			$table->smallInteger('genre2')->unsigned()->nullable();
			$table->smallInteger('licence')->unsigned();
			$table->text('synopsis')->nullable();
			$table->string('cover_url', 250)->nullable();
			$table->string('banner_url', 250)->nullable();
			$table->boolean('explicit_content')->default(false);
			$table->mediumInteger('total_chapters')->unsigned()->default(0);
			$table->mediumInteger('total_pages')->unsigned()->default(0);
		});
	}

	public function down()
	{
		Schema::drop('series');
	}

}
