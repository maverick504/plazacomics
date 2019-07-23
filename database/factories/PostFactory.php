<?php

use Faker\Generator as Faker;

$factory->define(App\Serie::class, function (Faker $faker) {
    return [
			'title' => $faker->sentence(rand(1,4)),
			'description' => $faker->optional()->sentence(rand(1,4)),
		  'state' => SERIE_STATE_ACTIVE,
			'images' => [],
			'explicit_content' => $faker->boolean(),
			'type' => App\Serie::TYPE_ILLUSTRATION
    ];
});
