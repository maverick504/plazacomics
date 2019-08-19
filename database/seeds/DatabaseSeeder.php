<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() === 'production') exit();

        Eloquent::unguard();

        // Delete(the rows from) all tables that will be seeded.
        $tablesToDelete = array(
          'posts', 'pages', 'chapters', 'series_authors', 'series', 'password_resets', 'users'
        );

        foreach($tablesToDelete as $table) {
          DB::table($table)->delete();
        }

        // Configuration
        $TOTAL_USERS = 3;
        $TOTAL_SERIES = 8;
        $TOTAL_POSTS = 15;

        // Execute seeders
        factory(App\User::class, $TOTAL_USERS)->create();
        factory(App\Serie::class, $TOTAL_SERIES)->create();

        // Get the generated users to generate data for those users
        $users = App\User::all();

        // Attach users as authors of series.
        App\Serie::all()->each(function ($serie) use ($users) {
          $serieAuthor = new App\SerieAuthor();
          $serieAuthor->serie_id = $serie->id;
          $serieAuthor->user_id = $users->random(1)->first()->id;
          $serieAuthor->save();
        });

        // Get the generated series to generate chapters and pages for those series
        $series = App\Serie::all();

        // Seed chapters
        $series->each(function ($serie) {
          $nChapters = rand(5, 10);

          $chapters = factory(App\Chapter::class, $nChapters)->make();
          $serie->chapters()->saveMany($chapters);
        });

        // Seed pages
        App\Chapter::all()->each(function ($chapter) {
          $nPages = rand(5, 8);

          for($i=0; $i<$nPages; $i++) {
            $page = new App\Page();
            $page->chapter_id = $chapter->id;
            $page->order = $i;
            $page->image_url = 'test-data/pages/' . strVal($i+1) . '.jpg';
            $page->save();
          }

          $chapter->total_pages = $nPages;
          $chapter->save();
        });

        // Recount chapters and pages
        $series->each(function ($serie) {
          $serie->recountChaptersAndPages();
        });

        // Seed Posts
        factory(App\Post::class, $TOTAL_POSTS)->create();

        // Attach users as creators of posts.
        App\Post::all()->each(function ($post) use ($users) {
          $post->user_id = $users->random(1)->first()->id;
          $post->save();
        });
    }
}
