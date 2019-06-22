<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLicencesTable extends Migration {

	public function up()
	{
		Schema::create('licences', function(Blueprint $table) {
			$table->smallIncrements('id');
			$table->string('language_key')->unique();
		});
		// Insert the available licences.
		DB::table('licences')->insert(array(
				'language_key' => 'copyright'
		));
		DB::table('licences')->insert(array(
				'language_key' => 'cc0'
		));
		DB::table('licences')->insert(array(
				'language_key' => 'cc_by'
		));
		DB::table('licences')->insert(array(
				'language_key' => 'cc_by-sa'
		));
		DB::table('licences')->insert(array(
				'language_key' => 'cc_by-nd'
		));
		DB::table('licences')->insert(array(
				'language_key' => 'cc_by-nc'
		));
		DB::table('licences')->insert(array(
				'language_key' => 'cc_by-nc-sa'
		));
		DB::table('licences')->insert(array(
				'language_key' => 'cc_by-nc-nd'
		));
	}

	public function down()
	{
		Schema::drop('licences');
	}
}
