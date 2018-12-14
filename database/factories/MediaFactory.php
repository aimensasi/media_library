<?php

use Faker\Generator as Faker;

$factory->define(App\Media::class, function (Faker $faker) {
	$name = $faker->word;
  return [
    "name" => $name,
		"path" => 'root/' . $name,
  ];
});
