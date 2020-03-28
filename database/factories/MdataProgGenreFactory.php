<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\MdataProgGenre;

$factory->define(MdataProgGenre::class, function (Faker $faker) {
    return [
        'genre_id' => $faker->unique()->buildingNumber,
        'name' => $faker->unique()->name,
    ];
});
