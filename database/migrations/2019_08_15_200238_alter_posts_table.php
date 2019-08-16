<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->text('title')->nullable()->change();

            $table->unsignedInteger('serie_id')->nullable();
            $table->unsignedBigInteger('chapter_id')->nullable();

            $table->foreign('serie_id')->references('id')->on('series');
            $table->foreign('chapter_id')->references('id')->on('chapters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->change();
            $table->text('title')->change();

            $table->dropForeign('posts_serie_id_foreign');
            $table->dropForeign('posts_chapter_id_foreign');

            $table->dropColumn('serie_id');
            $table->dropColumn('chapter_id');
        });
    }
}
