<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\Company;

$factory->define(Company::class, function (Faker $faker) { // 使用テーブル
    // ref https://stackoverflow.com/questions/43202886/laravel-seeding-multiple-unique-columns-with-faker

    // Generate unique imageId-languageCode combination
    return [
        'name' => $faker->company,
    ];
});
