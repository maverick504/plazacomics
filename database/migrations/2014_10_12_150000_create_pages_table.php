<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration {

	public function up()
	{
		Schema::create('pages', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('chapter_id')->unsigned();
			$table->smallInteger('order')->unsigned()->index();
			$table->string('image_url', 250);
		});
	}

	public function down()
	{
		Schema::drop('pages');
	}
}
