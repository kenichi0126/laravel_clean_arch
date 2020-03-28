<?php

use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\SystemInformation::class, function (Faker $faker) {
    return [
        'name' => $faker->randomDigitNotNull,
        'is_maintenance' => $is_maintenance ?? 0,
        'updated_at' => $faker->dateTime(),
    ];
});
