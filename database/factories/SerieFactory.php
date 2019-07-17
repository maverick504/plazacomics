<?php

use Faker\Generator as Faker;

$factory->define(App\Serie::class, function (Faker $faker) {
    return [
			'name' => $faker->unique()->sentence(rand(1,5)),
			'slug' => $faker->unique()->slug(),
		  'state' => SERIE_STATE_ACTIVE,
			'genre1' => 1,
			'genre2' => 2,
			'licence' => 1,
      'synopsis' => $faker->optional()->sentence(rand(5,15)),
      'cover_url' => null,
      'banner_url' => null,
      'explicit_content' => $faker->boolean(),
      'total_chapters' => 0,
      'total_pages' => 0
    ];
});
