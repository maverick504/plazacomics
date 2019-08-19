<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
			'title' => $faker->sentence(rand(1,4)),
			'description' => $faker->optional()->sentence(rand(0,8)),
			'images' => [
        [
          'hi_res' => [
            'url' => 'test-data/posts/1/hi_res.jpg',
            'width' => 2500,
            'height' => 2303
          ],
          'low_res' => [
            'url' => 'test-data/posts/1/low_res.jpg',
            'width' => 320,
            'height' => 295
          ],
          'thumbnail' => [
            'url' => 'test-data/posts/1/thumbnail.jpg',
            'width' => 160,
            'height' => 160
          ]
        ]
      ],
			'explicit_content' => $faker->boolean(),
      'publish_date' => $faker->dateTimeBetween('-14 days', '+7 days')->format('Y-m-d') . ' 00:00:00',
			'type' => App\Post::TYPE_ILLUSTRATION,
			'serie_id' => null,
			'chapter_id' => null
    ];
});
