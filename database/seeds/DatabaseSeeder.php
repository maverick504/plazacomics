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
          'pages', 'chapters', 'series_authors', 'series', 'password_resets', 'users'
        );

        foreach($tablesToDelete as $table) {
          DB::table($table)->delete();
        }

        // Execute seeders

        $TOTAL_USERS = 10;
        $TOTAL_SERIES = 10;

        factory(App\User::class, $TOTAL_USERS)->create();
        factory(App\Serie::class, $TOTAL_SERIES)->create();

        $users = App\User::all();

        // Attach users as authors of series.
        App\Serie::all()->each(function ($serie) use ($users) {
          $serieAuthor = new App\SerieAuthor();
          $serieAuthor->serie_id = $serie->id;
          $serieAuthor->user_id = $users->random(1)->first()->id;
          $serieAuthor->save();
        });

        // Seed chapters
        App\Serie::all()->each(function ($serie) {
          $chapters = factory(App\Chapter::class, rand(1, 5))->make();
          $serie->chapters()->saveMany($chapters);
        });

        // Seed pages
        App\Chapter::all()->each(function ($chapter) {
          $nPages = rand(5, 10);

          for($i=0; $i<$nPages; $i++) {
            $page = new App\Page();
            $page->chapter_id = $chapter->id;
            $page->order = $i;
            $page->image_url = 'seeder/pages/' . rand(1, 5) . '.png';
            $page->save();
          }

          $chapter->total_pages = $nPages;
          $chapter->save();
        });
    }
}
