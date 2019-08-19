<?php

use Faker\Generator as Faker;

$factory->define(App\Serie::class, function (Faker $faker) {
    return [
			'name' => $faker->unique()->sentence(rand(1,5)),
			'slug' => $faker->unique()->slug(),
		  'state' => SERIE_STATE_ACTIVE,
			'genre1' => rand(1,13),
			'genre2' => $faker->boolean()?rand(1,13):null,
			'licence' => rand(1,8),
      'synopsis' => $faker->optional()->sentence(rand(5,15)),
      'cover_url' => 'test-data/series/1/cover.jpg',
      'banner_url' => null,
      'explicit_content' => $faker->boolean(),
      'total_chapters' => 0,
      'total_pages' => 0
    ];
});
