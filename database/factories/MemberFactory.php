<?php

use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\Member::class, function (Faker $faker) {
    static $sponsor_id;
    static $password;
    return [
        'sponsor_id' => $sponsor_id ?? $sponsor_id = $faker->randomDigitNotNull,
        'family_name' => $faker->name,
        'given_name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password_digest' => $password ?? $password = bcrypt('secret'),
        'started_at' => date('Y-m-d'),
        'login_control_flag' => 0,
    ];
});
