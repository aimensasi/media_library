<?php

use Faker\Generator as Faker;

$factory->define(App\FileElement::class, function (Faker $faker) {

  return [
    "name" => $faker->firstName,
		"parent_id" => null
  ];
});
