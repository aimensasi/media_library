<?php

use Faker\Generator as Faker;

$factory->define(App\FileElement::class, function (Faker $faker) {

	$name = $faker->text(rand(5,10));

  return [
    "name" => $name
  ];
});
