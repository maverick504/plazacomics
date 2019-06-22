<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGenresTable extends Migration {

	public function up()
	{
		Schema::create('genres', function(Blueprint $table) {
			$table->smallIncrements('id');
			$table->string('language_key')->unique();
		});
		// Insert the available genres.
    DB::table('genres')->insert(array('language_key' => 'action'));
    DB::table('genres')->insert(array('language_key' => 'normal_love'));
    DB::table('genres')->insert(array('language_key' => 'boys_love'));
    DB::table('genres')->insert(array('language_key' => 'girls_love'));
    DB::table('genres')->insert(array('language_key' => 'comedy'));
    DB::table('genres')->insert(array('language_key' => 'drama'));
    DB::table('genres')->insert(array('language_key' => 'fantasy'));
		DB::table('genres')->insert(array('language_key' => 'science_fiction'));
    DB::table('genres')->insert(array('language_key' => 'slice_of_life'));
    DB::table('genres')->insert(array('language_key' => 'terror'));
		DB::table('genres')->insert(array('language_key' => 'adventure'));
		DB::table('genres')->insert(array('language_key' => 'one_shot'));
	}

	public function down()
	{
		Schema::drop('genres');
	}
}
