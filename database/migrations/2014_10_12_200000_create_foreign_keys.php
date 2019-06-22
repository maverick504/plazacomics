<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('series', function(Blueprint $table) {
			$table->foreign('genre1')->references('id')->on('genres')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('series', function(Blueprint $table) {
			$table->foreign('genre2')->references('id')->on('genres')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('series', function(Blueprint $table) {
			$table->foreign('licence')->references('id')->on('licences')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('chapters', function(Blueprint $table) {
			$table->foreign('serie_id')->references('id')->on('series')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('pages', function(Blueprint $table) {
			$table->foreign('chapter_id')->references('id')->on('chapters')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('series_authors', function(Blueprint $table) {
			$table->foreign('serie_id')->references('id')->on('series')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('series_authors', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
	}

	public function down()
	{
		Schema::table('series', function(Blueprint $table) {
			$table->dropForeign('series_genre1_foreign');
		});
		Schema::table('series', function(Blueprint $table) {
			$table->dropForeign('series_genre2_foreign');
		});
		Schema::table('series', function(Blueprint $table) {
			$table->dropForeign('series_licence_foreign');
		});
		Schema::table('chapters', function(Blueprint $table) {
			$table->dropForeign('chapters_serie_id_foreign');
		});
		Schema::table('pages', function(Blueprint $table) {
			$table->dropForeign('pages_chapter_id_foreign');
		});
		Schema::table('series_authors', function(Blueprint $table) {
			$table->dropForeign('series_authors_serie_id_foreign');
		});
		Schema::table('series_authors', function(Blueprint $table) {
			$table->dropForeign('series_authors_user_id_foreign');
		});
	}
}
