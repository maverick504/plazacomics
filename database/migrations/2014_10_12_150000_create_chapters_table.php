<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChaptersTable extends Migration {

	public function up()
	{
		Schema::create('chapters', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->timestamps();
			$table->integer('serie_id')->unsigned();
			$table->string('title');
			$table->string('slug');
			$table->string('thumbnail_url')->nullable();
			$table->datetime('relase_date');
			$table->smallInteger('total_pages')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('chapters');
	}
}
