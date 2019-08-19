<?php

use Faker\Generator as Faker;

$factory->define(App\Chapter::class, function (Faker $faker) {
    return [
			'title' => $faker->unique()->sentence(rand(1,5)),
			'slug' => $faker->unique()->slug(),
			'thumbnail_url' => null,
			'relase_date' => $faker->dateTimeBetween('-4 days', '+2 days')->format('Y-m-d') . ' 00:00:00',
			'total_pages' => 0
    ];
});
