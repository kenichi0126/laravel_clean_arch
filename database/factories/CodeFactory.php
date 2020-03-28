<?php

use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\Code::class, function (Faker $faker) {
    static $division;
    static $code;
    static $name;
    static $displayOrder;
    return [
        'division' => $division ?? $division = $faker->name,
        'code' => $code ?? $code = $faker->name,
        'name' => $name ?? $name = $faker->name,
        'display_order' => $displayOrder ?? $displayOrder = $faker->randomDigitNotNull,
    ];
});
