<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\TimeBox;

$factory->define(TimeBox::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()
            ->numberBetween(1, 99999),
        'region_id' => 1,
        'start_date' => $faker->date(),
        'duration' => 7,
        'version' => 1,
        'started_at' => $faker->date(),
        'ended_at' => $faker->date(),
        'panelers_number' => $faker->numberBetween(1, 99999),
        'households_number' => $faker->numberBetween(1, 99999),
    ];
});
