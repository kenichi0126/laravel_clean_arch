<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\Programs;

$factory->define(Programs::class, function (Faker $faker) { // 使用テーブル
    // Generate unique imageId-languageCode combination
    return [
        'prog_id' => $faker->text($maxNbChars = 32),
        'time_box_id' => $faker->unique()->numberBetween(0, 10000),
        'date' => '2017-08-02',
        'real_started_at' => '2017-08-02 08:30:00',
        'real_ended_at' => '2017-08-09 23:30:00',
        'channel_id' => '3',
    ];
});
