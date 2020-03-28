<?php

use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\Sponsor::class, function (Faker $faker) {
    static $status;
    return [
        'name' => $faker->name,
        'status' => $status ?? $status = 'contracted',
        'started_at' => date('Y-m-d'),
    ];
});
