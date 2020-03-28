<?php

use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\SponsorTrial::class, function (Faker $faker) {
    static $sponsor_id;
    static $settings;

    return [
        'sponsor_id' => $sponsor_id,
        'settings' => $settings ?? $settings = [
            'started_at' => '2000-01-01',
            'ended_at' => '2999-12-31',
        ],
    ];
});
